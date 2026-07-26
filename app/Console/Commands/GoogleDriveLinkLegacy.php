<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use App\Services\GoogleDriveService;
use Illuminate\Console\Command;

class GoogleDriveLinkLegacy extends Command
{
    protected $signature = 'google-drive:link-legacy
        {--berkas-folder=1zt7_bATmqxQ6fPs_X7erBP50DiE3W5-E : ID folder Drive "Berkas" (AppSheet lama)}
        {--pembayaran-folder=1WE90N21kPPUwBLo2Qc3YPlvImn96avnK : ID folder Drive "BerkasPembayaran" (AppSheet lama)}
        {--apply : Simpan hasil pencocokan ke database. Tanpa opsi ini, cuma preview (dry-run).}';

    protected $description = 'Cocokkan subfolder & file mahasiswa yang sudah ada di Google Drive (bekas AppSheet) ke record mahasiswa, tanpa upload ulang file';

    private const BERKAS_TYPE_COLUMNS = [
        'IJAZAH' => 'ijazah_drive_url',
        'KTP' => 'ktp_drive_url',
        'KK' => 'kk_drive_url',
        'FOTO' => 'pas_foto_drive_url',
    ];

    public function handle(GoogleDriveService $googleDrive): int
    {
        if (!$googleDrive->configured()) {
            $this->error('Google Drive belum dikonfigurasi (cek GOOGLE_DRIVE_* di .env).');

            return self::FAILURE;
        }

        $mahasiswas = Mahasiswa::query()
            ->with(['berkas', 'pembayarans'])
            ->get(['id', 'nama_mahasiswa', 'kode_pmb', 'google_drive_folder_id', 'google_drive_pembayaran_folder_id']);

        $byName = [];
        foreach ($mahasiswas as $mahasiswa) {
            $byName[$this->normalize($mahasiswa->nama_mahasiswa)][] = $mahasiswa;
        }

        $this->processFolder(
            'Berkas',
            $googleDrive,
            $googleDrive->listFolders($this->option('berkas-folder')),
            $byName,
            'google_drive_folder_id',
            'google_drive_folder_url',
            fn (Mahasiswa $mahasiswa, array $folder) => $this->linkBerkasFiles($googleDrive, $mahasiswa, $folder),
        );

        $this->processFolder(
            'BerkasPembayaran',
            $googleDrive,
            $googleDrive->listFolders($this->option('pembayaran-folder')),
            $byName,
            'google_drive_pembayaran_folder_id',
            'google_drive_pembayaran_folder_url',
            fn (Mahasiswa $mahasiswa, array $folder) => $this->linkPembayaranFile($googleDrive, $mahasiswa, $folder),
        );

        if (!$this->option('apply')) {
            $this->newLine();
            $this->warn('Ini masih PREVIEW (dry-run). Jalankan ulang dengan --apply untuk benar-benar menyimpan ke database.');
        }

        return self::SUCCESS;
    }

    private function processFolder(
        string $label,
        GoogleDriveService $googleDrive,
        array $driveFolders,
        array $byName,
        string $idColumn,
        string $urlColumn,
        callable $fileLinker,
    ): void {
        $this->newLine();
        $this->info('=== ' . $label . ' (' . count($driveFolders) . ' folder ditemukan di Drive) ===');

        $matched = 0;
        $alreadyLinked = 0;
        $filesMatched = 0;
        $conflicts = [];
        $ambiguous = [];
        $noMatch = [];

        foreach ($driveFolders as $folder) {
            $key = $this->normalize($folder['name']);
            $candidates = $byName[$key] ?? [];

            if (count($candidates) === 0) {
                $noMatch[] = $folder['name'];
                continue;
            }

            if (count($candidates) > 1) {
                $ambiguous[] = $folder['name'] . ' (' . count($candidates) . ' mahasiswa dengan nama sama)';
                continue;
            }

            $mahasiswa = $candidates[0];

            if (filled($mahasiswa->{$idColumn}) && $mahasiswa->{$idColumn} !== $folder['id']) {
                $conflicts[] = "{$mahasiswa->nama_mahasiswa} ({$mahasiswa->kode_pmb}): sudah tertaut ke folder lain, folder '{$folder['name']}' dilewati";
                continue;
            }

            if ($mahasiswa->{$idColumn} === $folder['id']) {
                $alreadyLinked++;
            } else {
                $matched++;

                if ($this->option('apply')) {
                    $mahasiswa->forceFill([
                        $idColumn => $folder['id'],
                        $urlColumn => $folder['webViewLink'] ?? null,
                    ])->save();
                } else {
                    $this->line("  cocok: {$folder['name']} -> {$mahasiswa->nama_mahasiswa} ({$mahasiswa->kode_pmb})");
                }
            }

            $filesMatched += $fileLinker($mahasiswa, $folder);
        }

        $this->line(($this->option('apply') ? 'Ditautkan' : 'Akan ditautkan') . ": {$matched}");
        $this->line("Sudah tertaut sebelumnya: {$alreadyLinked}");
        $this->line(($this->option('apply') ? 'File spesifik ditautkan' : 'File spesifik akan ditautkan') . ": {$filesMatched}");

        if ($conflicts) {
            $this->warn('Konflik (folder lama beda dengan yang ditemukan, dilewati, cek manual):');
            foreach ($conflicts as $line) {
                $this->line("  - {$line}");
            }
        }

        if ($ambiguous) {
            $this->warn('Ambigu (nama folder cocok ke lebih dari 1 mahasiswa, dilewati, cek manual):');
            foreach ($ambiguous as $line) {
                $this->line("  - {$line}");
            }
        }

        if ($noMatch) {
            $this->warn('Folder Drive tanpa mahasiswa yang cocok di database:');
            foreach ($noMatch as $line) {
                $this->line("  - {$line}");
            }
        }
    }

    /**
     * Match files inside a student's legacy "Berkas" folder to ijazah/ktp/kk/etc columns
     * by looking for an exact type keyword token in the filename. Returns files matched/would-match.
     */
    private function linkBerkasFiles(GoogleDriveService $googleDrive, Mahasiswa $mahasiswa, array $folder): int
    {
        $berkas = $mahasiswa->berkas;

        if (!$berkas) {
            return 0;
        }

        $byType = [];
        foreach ($googleDrive->listFiles($folder['id']) as $file) {
            $tokens = preg_split('/[^A-Z0-9]+/', strtoupper($file['name']), -1, PREG_SPLIT_NO_EMPTY);
            $types = array_intersect($tokens, array_keys(self::BERKAS_TYPE_COLUMNS));

            if (count($types) === 1) {
                $byType[array_values($types)[0]][] = $file;
            }
        }

        $count = 0;
        $updates = [];

        foreach (self::BERKAS_TYPE_COLUMNS as $type => $column) {
            $matches = $byType[$type] ?? [];

            if (count($matches) !== 1 || filled($berkas->{$column})) {
                continue;
            }

            $count++;
            $updates[$column] = $matches[0]['webViewLink'] ?? null;
        }

        if ($updates && $this->option('apply')) {
            $berkas->forceFill($updates)->save();
        }

        return $count;
    }

    /**
     * Link a legacy bukti-bayar file to the mahasiswa's payment record, only when the match
     * is unambiguous: exactly one file in the folder and exactly one payment record.
     */
    private function linkPembayaranFile(GoogleDriveService $googleDrive, Mahasiswa $mahasiswa, array $folder): int
    {
        $files = $googleDrive->listFiles($folder['id']);
        $pembayarans = $mahasiswa->pembayarans;

        if (count($files) !== 1 || $pembayarans->count() !== 1) {
            return 0;
        }

        $pembayaran = $pembayarans->first();

        if (filled($pembayaran->bukti_bayar_drive_url)) {
            return 0;
        }

        if ($this->option('apply')) {
            $pembayaran->forceFill(['bukti_bayar_drive_url' => $files[0]['webViewLink'] ?? null])->save();
        }

        return 1;
    }

    private function normalize(?string $value): string
    {
        $value = strtoupper((string) $value);
        $value = preg_replace('/[^A-Z0-9]+/', ' ', $value);

        return trim($value);
    }
}
