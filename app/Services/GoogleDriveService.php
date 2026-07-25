<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

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
     * List every direct subfolder of the given Drive folder (id, name, webViewLink), paginated.
     */
    public function listFolders(string $parentFolderId): array
    {
        return $this->listChildren($parentFolderId, "mimeType = '" . self::DRIVE_FOLDER_MIME . "'");
    }

    /**
     * List every file (non-folder) directly inside the given Drive folder (id, name, webViewLink), paginated.
     */
    public function listFiles(string $parentFolderId): array
    {
        return $this->listChildren($parentFolderId, "mimeType != '" . self::DRIVE_FOLDER_MIME . "'");
    }

    private function listChildren(string $parentFolderId, string $mimeTypeClause): array
    {
        $items = [];
        $pageToken = null;

        do {
            $query = [
                'q' => sprintf(
                    "'%s' in parents and %s and trashed = false",
                    $this->escapeQuery($parentFolderId),
                    $mimeTypeClause
                ),
                'fields' => 'nextPageToken,files(id,name,webViewLink)',
                'pageSize' => 1000,
            ];

            if ($pageToken) {
                $query['pageToken'] = $pageToken;
            }

            $response = Http::withToken($this->accessToken())->get('https://www.googleapis.com/drive/v3/files', $query);

            if ($response->failed()) {
                throw new RuntimeException('Ambil daftar dari Google Drive gagal: ' . $response->body());
            }

            $items = array_merge($items, $response->json('files') ?? []);
            $pageToken = $response->json('nextPageToken');
        } while ($pageToken);

        return $items;
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
