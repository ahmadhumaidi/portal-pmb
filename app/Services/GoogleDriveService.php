<?php

namespace App\Services;

use App\Models\Mahasiswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleDriveService
{
    private const DRIVE_FOLDER_MIME = 'application/vnd.google-apps.folder';

    public function configured(): bool
    {
        return (bool) config('services.google_drive.enabled')
            && filled(config('services.google_drive.parent_folder_id'))
            && filled(config('services.google_drive.service_account_json'))
            && is_file(config('services.google_drive.service_account_json'));
    }

    public function uploadStudentFile(Mahasiswa $mahasiswa, UploadedFile $file, string $label): ?array
    {
        if (!$this->configured()) {
            return null;
        }

        $folder = $this->studentFolder($mahasiswa);
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'file');
        $fileName = Str::slug($label, ' ') . '.' . $extension;
        $existingFileId = $this->findFile($fileName, $folder['id']);

        $metadata = [
            'name' => $fileName,
            'parents' => [$folder['id']],
        ];

        $url = 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,webViewLink';
        $request = Http::withToken($this->accessToken())
            ->attach('metadata', json_encode($metadata), 'metadata.json', ['Content-Type' => 'application/json'])
            ->attach('file', file_get_contents($file->getRealPath()), $fileName, ['Content-Type' => $file->getMimeType() ?: 'application/octet-stream']);

        if ($existingFileId) {
            $url = "https://www.googleapis.com/upload/drive/v3/files/{$existingFileId}?uploadType=multipart&fields=id,name,webViewLink";
            $request = Http::withToken($this->accessToken())
                ->attach('metadata', json_encode(['name' => $fileName]), 'metadata.json', ['Content-Type' => 'application/json'])
                ->attach('file', file_get_contents($file->getRealPath()), $fileName, ['Content-Type' => $file->getMimeType() ?: 'application/octet-stream']);
            $response = $request->patch($url);
        } else {
            $response = $request->post($url);
        }

        if ($response->failed()) {
            throw new RuntimeException('Upload ke Google Drive gagal: ' . $response->body());
        }

        return $response->json();
    }

    private function studentFolder(Mahasiswa $mahasiswa): array
    {
        if ($mahasiswa->google_drive_folder_id) {
            return [
                'id' => $mahasiswa->google_drive_folder_id,
                'webViewLink' => $mahasiswa->google_drive_folder_url,
            ];
        }

        $folderName = trim($mahasiswa->nama_mahasiswa ?: $mahasiswa->kode_pmb);
        $parentFolderId = config('services.google_drive.parent_folder_id');
        $folder = $this->findFolder($folderName, $parentFolderId) ?? $this->createFolder($folderName, $parentFolderId);

        $mahasiswa->forceFill([
            'google_drive_folder_id' => $folder['id'] ?? null,
            'google_drive_folder_url' => $folder['webViewLink'] ?? null,
        ])->save();

        return $folder;
    }

    private function findFolder(string $name, string $parentFolderId): ?array
    {
        $query = sprintf(
            "name = '%s' and mimeType = '%s' and '%s' in parents and trashed = false",
            $this->escapeQuery($name),
            self::DRIVE_FOLDER_MIME,
            $this->escapeQuery($parentFolderId)
        );

        $response = Http::withToken($this->accessToken())->get('https://www.googleapis.com/drive/v3/files', [
            'q' => $query,
            'fields' => 'files(id,name,webViewLink)',
            'pageSize' => 1,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Cek folder Google Drive gagal: ' . $response->body());
        }

        return $response->json('files.0');
    }

    private function createFolder(string $name, string $parentFolderId): array
    {
        $response = Http::withToken($this->accessToken())->post('https://www.googleapis.com/drive/v3/files?fields=id,name,webViewLink', [
            'name' => $name,
            'mimeType' => self::DRIVE_FOLDER_MIME,
            'parents' => [$parentFolderId],
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Buat folder Google Drive gagal: ' . $response->body());
        }

        return $response->json();
    }

    private function findFile(string $name, string $folderId): ?string
    {
        $query = sprintf(
            "name = '%s' and '%s' in parents and trashed = false",
            $this->escapeQuery($name),
            $this->escapeQuery($folderId)
        );

        $response = Http::withToken($this->accessToken())->get('https://www.googleapis.com/drive/v3/files', [
            'q' => $query,
            'fields' => 'files(id)',
            'pageSize' => 1,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Cek file Google Drive gagal: ' . $response->body());
        }

        return $response->json('files.0.id');
    }

    private function accessToken(): string
    {
        $credentials = $this->credentials();
        $cacheKey = 'google_drive_service_account_token_' . md5($credentials['client_email']);

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($credentials) {
            $now = time();
            $header = ['alg' => 'RS256', 'typ' => 'JWT'];
            $payload = [
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/drive',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ];

            $unsignedJwt = $this->base64Url(json_encode($header)) . '.' . $this->base64Url(json_encode($payload));
            openssl_sign($unsignedJwt, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
            $jwt = $unsignedJwt . '.' . $this->base64Url($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->failed()) {
                throw new RuntimeException('Login Google Drive gagal: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    private function credentials(): array
    {
        $path = config('services.google_drive.service_account_json');
        $credentials = json_decode(file_get_contents($path), true);

        if (!is_array($credentials) || empty($credentials['client_email']) || empty($credentials['private_key'])) {
            throw new RuntimeException('Credential Google Drive tidak valid.');
        }

        return $credentials;
    }

    private function escapeQuery(string $value): string
    {
        return str_replace("'", "\\'", $value);
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
