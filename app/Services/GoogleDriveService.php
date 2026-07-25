<?php

namespace App\Services;

use App\Models\Mahasiswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class GoogleDriveService
{
    private const DRIVE_FOLDER_MIME = 'application/vnd.google-apps.folder';

    public function configured(): bool
    {
        return (bool) config('services.google_drive.enabled')
            && filled(config('services.google_drive.parent_folder_id'))
            && filled(config('services.google_drive.client_id'))
            && filled(config('services.google_drive.client_secret'))
            && filled(config('services.google_drive.refresh_token'));
    }

    /**
     * Store a file on the local "public" disk, back it up to Google Drive, then
     * delete the local copy once the backup succeeds so files don't pile up on the server.
     */
    public function storeAndBackup(Mahasiswa $mahasiswa, UploadedFile $file, string $directory, string $label): array
    {
        $path = $file->store($directory, 'public');
        $failed = false;

        try {
            if ($this->uploadStudentFile($mahasiswa, $file, $label)) {
                Storage::disk('public')->delete($path);
            }
        } catch (Throwable $exception) {
            report($exception);
            $failed = true;
        }

        return ['path' => $path, 'failed' => $failed];
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
        $clientId = config('services.google_drive.client_id');
        $cacheKey = 'google_drive_oauth_token_' . md5($clientId);

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($clientId) {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'refresh_token',
                'client_id' => $clientId,
                'client_secret' => config('services.google_drive.client_secret'),
                'refresh_token' => config('services.google_drive.refresh_token'),
            ]);

            if ($response->failed()) {
                throw new RuntimeException('Login Google Drive gagal: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    private function escapeQuery(string $value): string
    {
        return str_replace("'", "\\'", $value);
    }
}
