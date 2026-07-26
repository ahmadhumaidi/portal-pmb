<?php

namespace App\Http\Controllers;

use App\Services\PaddleOcrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class OcrController extends Controller
{
    private const SESSION_KEY = 'ocr_draft';

    private array $documentTypes = [
        'ktp' => 'KTP',
        'kk' => 'Kartu Keluarga',
        'ijazah' => 'Ijazah',
    ];

    public function __construct(private PaddleOcrService $ocr)
    {
    }

    public function index(): View
    {
        return view('ocr.index', [
            'documentTypes' => $this->documentTypes,
            'documents' => session(self::SESSION_KEY . '.documents', []),
            'ocrConfigured' => $this->ocr->configured(),
        ]);
    }

    public function proses(Request $request, string $jenis): RedirectResponse
    {
        abort_unless(isset($this->documentTypes[$jenis]), 404);

        $request->validate([
            'dokumen' => ['required', 'file', 'max:5120'],
        ]);

        $token = session(self::SESSION_KEY . '.token') ?? (string) Str::uuid();
        $directory = 'ocr-temp/' . $token;

        $existingPath = session(self::SESSION_KEY . ".documents.{$jenis}.path");

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        $path = $request->file('dokumen')->store($directory, 'public');
        $label = $this->documentTypes[$jenis];

        try {
            $result = $this->ocr->extract(
                $jenis,
                Storage::disk('public')->path($path),
                Storage::disk('public')->mimeType($path)
            );

            session([
                self::SESSION_KEY . '.token' => $token,
                self::SESSION_KEY . ".documents.{$jenis}" => [
                    'path' => $path,
                    'status' => 'berhasil',
                    'confidence' => $result['confidence'],
                    'data' => $result['data'],
                    'error' => null,
                ],
            ]);

            return back()->with('success', "Pembacaan {$label} selesai.");
        } catch (RuntimeException $e) {
            session([
                self::SESSION_KEY . '.token' => $token,
                self::SESSION_KEY . ".documents.{$jenis}" => [
                    'path' => $path,
                    'status' => 'gagal',
                    'confidence' => null,
                    'data' => null,
                    'error' => $e->getMessage(),
                ],
            ]);

            return back()->with('warning', "Pembacaan {$label} gagal: {$e->getMessage()}");
        }
    }

    public function terapkan(): RedirectResponse
    {
        $documents = session(self::SESSION_KEY . '.documents', []);

        // KTP is the primary legal document, so it should win over KK/Ijazah on overlapping fields.
        $mergeOrder = ['ijazah', 'kk', 'ktp'];
        $prefill = [];

        foreach ($mergeOrder as $jenis) {
            $result = $documents[$jenis] ?? null;

            if (($result['status'] ?? null) === 'berhasil') {
                $prefill = array_merge($prefill, array_filter($result['data'] ?? [], fn ($value) => filled($value)));
            }
        }

        if (empty($prefill)) {
            return back()->with('warning', 'Belum ada hasil bacaan yang bisa diterapkan. Proses minimal satu dokumen terlebih dahulu.');
        }

        if (isset($prefill['jenis_kelamin'])) {
            $prefill['jenis_kelamin'] = str_starts_with(strtoupper(trim($prefill['jenis_kelamin'])), 'P') ? 'P' : 'L';
        }

        return redirect()
            ->route('mahasiswa.create')
            ->withInput($prefill)
            ->with('success', 'Hasil bacaan AI OCR sudah dituangkan ke form. Lengkapi kampus/jurusan dan periksa kembali sebelum menyimpan.')
            ->with('ocr_draft_active', true);
    }

    public function reset(): RedirectResponse
    {
        $token = session(self::SESSION_KEY . '.token');

        if ($token) {
            Storage::disk('public')->deleteDirectory('ocr-temp/' . $token);
        }

        session()->forget(self::SESSION_KEY);

        return redirect()->route('ocr.index')->with('success', 'Sesi AI OCR direset.');
    }
}
