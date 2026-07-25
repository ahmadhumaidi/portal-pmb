<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GoogleDriveAuthorize extends Command
{
    protected $signature = 'google-drive:authorize';

    protected $description = 'Generate a Google Drive OAuth refresh token for GOOGLE_DRIVE_REFRESH_TOKEN';

    private const REDIRECT_URI = 'http://localhost:8080';

    public function handle(): int
    {
        $clientId = config('services.google_drive.client_id');
        $clientSecret = config('services.google_drive.client_secret');

        if (blank($clientId) || blank($clientSecret)) {
            $this->error('Isi dulu GOOGLE_DRIVE_CLIENT_ID dan GOOGLE_DRIVE_CLIENT_SECRET di .env sebelum menjalankan perintah ini.');

            return self::FAILURE;
        }

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => self::REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/drive',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        $this->info('1. Buka URL berikut di BROWSER KOMPUTER KAMU (bukan di VPS), login pakai akun Google yang mau dipakai:');
        $this->line($authUrl);
        $this->newLine();
        $this->info('2. Klik "Allow/Izinkan". Browser akan coba redirect ke ' . self::REDIRECT_URI . '/?code=... dan gagal terhubung — itu normal, biarkan saja.');
        $this->info('3. Copy nilai parameter "code" dari address bar browser (antara "code=" dan "&scope").');
        $this->newLine();

        $code = $this->ask('Paste kode otorisasi (code) di sini');

        if (blank($code)) {
            $this->error('Kode tidak boleh kosong.');

            return self::FAILURE;
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => trim($code),
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => self::REDIRECT_URI,
            'grant_type' => 'authorization_code',
        ]);

        if ($response->failed()) {
            $this->error('Gagal tukar kode: ' . $response->body());

            return self::FAILURE;
        }

        $refreshToken = $response->json('refresh_token');

        if (blank($refreshToken)) {
            $this->error('Tidak dapat refresh_token. Response: ' . $response->body());
            $this->warn('Kemungkinan akun ini sudah pernah authorize app ini sebelumnya. Cabut akses di https://myaccount.google.com/permissions lalu ulangi.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Berhasil! Simpan baris ini ke .env:');
        $this->line('GOOGLE_DRIVE_REFRESH_TOKEN=' . $refreshToken);

        return self::SUCCESS;
    }
}
