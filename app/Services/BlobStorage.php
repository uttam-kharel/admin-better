<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

/**
 * Stores uploaded files (images, CVs) on Vercel Blob (free tier) when a
 * BLOB_READ_WRITE_TOKEN is configured. Without a token it falls back to a
 * base64 data URI, which persists inside the committed SQLite DB — this keeps
 * uploads working everywhere (local dev, previews, and Vercel) without setup.
 */
class BlobStorage
{
    protected const ENDPOINT = 'https://blob.vercel-storage.com';

    /**
     * @return string A public URL (https://...) or data URI to persist in the DB.
     */
    public static function store(UploadedFile $file, string $prefix = 'uploads'): string
    {
        $token = (string) config('services.blob.token', '');

        if ($token !== '') {
            $url = self::uploadToBlob($file, $prefix, $token);
            if ($url !== null) {
                return $url;
            }
        }

        return 'data:'.$file->getMimeType().';base64,'.base64_encode($file->get());
    }

    /**
     * Vercel Blob documented server upload:
     *   PUT https://blob.vercel-storage.com/{pathname}
     *   Authorization: Bearer <token>
     *   x-add-random-suffix: 1
     *   x-content-type: <mime>
     *   body = raw file bytes
     *
     * Response JSON contains the public CDN "url".
     */
    protected static function uploadToBlob(UploadedFile $file, string $prefix, string $token): ?string
    {
        try {
            $pathname = $prefix.'/'.date('Y/m').'/'.uniqid().'-'.preg_replace('/[^a-zA-Z0-9._-]/', '-', $file->getClientOriginalName());

            $response = Http::withToken($token)
                ->withHeaders([
                    'x-add-random-suffix' => '1',
                    'x-content-type' => $file->getMimeType() ?: 'application/octet-stream',
                ])
                ->withBody(file_get_contents($file->getRealPath()), $file->getMimeType() ?: 'application/octet-stream')
                ->put(self::ENDPOINT.'/'.$pathname);

            if ($response->successful()) {
                $url = $response->json('url');
                if (is_string($url) && $url !== '') {
                    return $url;
                }
            }
        } catch (\Throwable $e) {
            // Fall through to the base64 fallback; never break an upload.
        }

        return null;
    }
}
