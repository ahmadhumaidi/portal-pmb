<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaddleOcrService
{
    /**
     * Fields to extract per document type, mapped to Mahasiswa column names
     * so results can be applied to biodata directly.
     */
    private array $fieldsByDocument = [
        'ktp' => ['nik', 'nama_mahasiswa', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'agama', 'kewarganegaraan'],
        'kk' => ['nama_mahasiswa', 'nama_ibu', 'alamat'],
        'ijazah' => ['nama_mahasiswa', 'asal_sekolah', 'tahun_lulus'],
    ];

    public function configured(): bool
    {
        try {
            return Http::timeout(2)->get($this->baseUrl() . '/health')->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function baseUrl(): string
    {
        return rtrim(config('services.paddle_ocr.base_url'), '/');
    }

    public function supports(string $documentType): bool
    {
        return isset($this->fieldsByDocument[$documentType]);
    }

    /**
     * Send the document image to the local PaddleOCR service and return
     * ['confidence' => int, 'data' => array].
     */
    public function extract(string $documentType, string $absolutePath, string $mimeType): array
    {
        if (! isset($this->fieldsByDocument[$documentType])) {
            throw new RuntimeException("Jenis dokumen \"{$documentType}\" tidak didukung untuk OCR.");
        }

        // PaddleOCR inference on this host's CPU runs ~30s per document;
        // stay comfortably under nginx's fastcgi_read_timeout (120s) so a
        // slow-but-working request fails here with a readable message
        // instead of nginx cutting the connection first with a raw 504.
        $response = Http::timeout(100)
            ->attach('file', file_get_contents($absolutePath), basename($absolutePath))
            ->post($this->baseUrl() . '/ocr');

        if ($response->failed()) {
            throw new RuntimeException('Permintaan ke PaddleOCR gagal: ' . $response->body());
        }

        $text = trim((string) $response->json('text'));
        $ocrConfidence = (float) $response->json('confidence', 0);

        if (blank($text)) {
            throw new RuntimeException('Tidak ada teks yang terbaca dari dokumen.');
        }

        $data = $this->extractFields($documentType, $text);
        $fields = $this->fieldsByDocument[$documentType];
        $fieldsFound = count(array_filter($data, fn ($value) => filled($value)));
        $extractionRatio = count($fields) > 0 ? $fieldsFound / count($fields) : 0;

        $confidence = (int) round((0.6 * $ocrConfidence) + (0.4 * $extractionRatio * 100));

        return [
            'confidence' => max(0, min(100, $confidence)),
            'data' => $data,
        ];
    }

    private function extractFields(string $documentType, string $text): array
    {
        return match ($documentType) {
            'ktp' => $this->extractKtp($text),
            'kk' => $this->extractKk($text),
            'ijazah' => $this->extractIjazah($text),
            default => [],
        };
    }

    private function extractKtp(string $text): array
    {
        $data = array_fill_keys($this->fieldsByDocument['ktp'], null);

        if (preg_match('/NIK\s*[:.]?\s*(\d{16})/i', $text, $m)) {
            $data['nik'] = $m[1];
        }

        if (preg_match('/Nama\s*[:.]?\s*([A-Z][A-Z .\'\-]{2,})/', $text, $m)) {
            $data['nama_mahasiswa'] = trim($m[1]);
        }

        if (preg_match('/Tempat\s*\/?\s*Tgl\s*Lahir\s*[:.]?\s*([A-Za-z .\'\-]+),\s*(\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4})/i', $text, $m)) {
            $data['tempat_lahir'] = trim($m[1]);
            $data['tanggal_lahir'] = $this->normalizeDate($m[2]);
        }

        if (preg_match('/Jenis\s*Kelamin\s*[:.]?\s*(LAKI[- ]?LAKI|PEREMPUAN)/i', $text, $m)) {
            $data['jenis_kelamin'] = stripos($m[1], 'PEREMPUAN') !== false ? 'P' : 'L';
        }

        if (preg_match('/Alamat\s*[:.]?\s*([^\n]+)/i', $text, $m)) {
            $data['alamat'] = trim($m[1]);
        }

        if (preg_match('/Agama\s*[:.]?\s*([A-Za-z]+)/i', $text, $m)) {
            $data['agama'] = ucfirst(strtolower($m[1]));
        }

        if (preg_match('/Kewarganegaraan\s*[:.]?\s*(WNI|WNA)/i', $text, $m)) {
            $data['kewarganegaraan'] = strtoupper($m[1]);
        }

        return $data;
    }

    private function extractKk(string $text): array
    {
        $data = array_fill_keys($this->fieldsByDocument['kk'], null);

        if (preg_match('/Nama\s*Kepala\s*Keluarga\s*[:.]?\s*([A-Z][A-Z .\'\-]{2,})/i', $text, $m)) {
            $data['nama_mahasiswa'] = trim($m[1]);
        }

        if (preg_match('/Alamat\s*[:.]?\s*([^\n]+)/i', $text, $m)) {
            $data['alamat'] = trim($m[1]);
        }

        // Best-effort: find the family-member row whose "hubungan keluarga" column
        // reads Ibu/Istri and take the name that precedes it on the same OCR line.
        foreach (explode("\n", $text) as $line) {
            if (preg_match('/^([A-Z][A-Z .\'\-]{2,}?)\s+\d{10,16}.*\b(IBU|ISTRI)\b/i', $line, $m)) {
                $data['nama_ibu'] = trim($m[1]);
                break;
            }
        }

        return $data;
    }

    private function extractIjazah(string $text): array
    {
        $data = array_fill_keys($this->fieldsByDocument['ijazah'], null);

        if (preg_match('/Nama\s*[:.]?\s*([A-Z][A-Z .\'\-]{2,})/i', $text, $m)) {
            $data['nama_mahasiswa'] = trim($m[1]);
        }

        if (preg_match('/((?:SMA|SMK|MA|MTS|MADRASAH)[A-Z0-9 .\'\-]{2,})/i', $text, $m)) {
            $data['asal_sekolah'] = trim($m[1]);
        }

        if (preg_match('/(?:Tahun\s*(?:Pelajaran|Ajaran)|Lulus(?:\s*Tahun)?)\D{0,20}(\d{4})/i', $text, $m)) {
            $data['tahun_lulus'] = $m[1];
        } elseif (preg_match('/\b(19[9]\d|20[0-4]\d)\b/', $text, $m)) {
            $data['tahun_lulus'] = $m[1];
        }

        return $data;
    }

    private function normalizeDate(string $raw): ?string
    {
        if (! preg_match('/(\d{1,2})[-\/](\d{1,2})[-\/](\d{2,4})/', trim($raw), $m)) {
            return null;
        }

        [, $day, $month, $year] = $m;

        if (strlen($year) === 2) {
            $year = ((int) $year > 30 ? '19' : '20') . $year;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
