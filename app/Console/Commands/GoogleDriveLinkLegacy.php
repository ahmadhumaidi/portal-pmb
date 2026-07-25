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

    protected $description = 'Cocokkan subfolder mahasiswa yang sudah ada di Google Drive (bekas AppSheet) ke record mahasiswa, tanpa upload ulang file';

    public function handle(GoogleDriveService $googleDrive): int
    {
        if (!$googleDrive->configured()) {
            $this->error('Google Drive belum dikonfigurasi (cek GOOGLE_DRIVE_* di .env).');

            return self::FAILURE;
        }

        $mahasiswas = Mahasiswa::query()->get(['id', 'nama_mahasiswa', 'kode_pmb', 'google_drive_folder_id', 'google_drive_pembayaran_folder_id']);

        $byName = [];
        foreach ($mahasiswas as $mahasiswa) {
            $byName[$this->normalize($mahasiswa->nama_mahasiswa)][] = $mahasiswa;
        }

        $this->processFolder(
            'Berkas',
            $googleDrive->listFolders($this->option('berkas-folder')),
            $byName,
            'google_drive_folder_id',
            'google_drive_folder_url',
        );

        $this->processFolder(
            'BerkasPembayaran',
            $googleDrive->listFolders($this->option('pembayaran-folder')),
            $byName,
            'google_drive_pembayaran_folder_id',
            'google_drive_pembayaran_folder_url',
        );

        if (!$this->option('apply')) {
            $this->newLine();
            $this->warn('Ini masih PREVIEW (dry-run). Jalankan ulang dengan --apply untuk benar-benar menyimpan ke database.');
        }

        return self::SUCCESS;
    }

    private function processFolder(string $label, array $driveFolders, array $byName, string $idColumn, string $urlColumn): void
    {
        $this->newLine();
        $this->info('=== ' . $label . ' (' . count($driveFolders) . ' folder ditemukan di Drive) ===');

        $matched = 0;
        $alreadyLinked = 0;
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

            if ($mahasiswa->{$idColumn} === $folder['id']) {
                $alreadyLinked++;
                continue;
            }

            if (filled($mahasiswa->{$idColumn})) {
                $conflicts[] = "{$mahasiswa->nama_mahasiswa} ({$mahasiswa->kode_pmb}): sudah tertaut ke folder lain, folder '{$folder['name']}' dilewati";
                continue;
            }

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

        $this->line(($this->option('apply') ? 'Ditautkan' : 'Akan ditautkan') . ": {$matched}");
        $this->line("Sudah tertaut sebelumnya: {$alreadyLinked}");

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

    private function normalize(?string $value): string
    {
        $value = strtoupper((string) $value);
        $value = preg_replace('/[^A-Z0-9]+/', ' ', $value);

        return trim($value);
    }
}
