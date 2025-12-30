<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PdfImageHelper
{
    /**
     * Fetch a remote image and return a data URI (or null on failure).
     *
     * Important: this fetches the image server-side and embeds it as base64.
     */
    public static function fetchImageAsDataUri(string $url): ?string
    {
        try {
            $response = Http::timeout(10)->get($url);

            if (! $response->successful()) {
                Log::warning("Image fetch responded non-success: {$url} status=" . $response->status());
                return null;
            }

            $body = $response->body();
            if ($body === null || $body === '') {
                return null;
            }

            $mime = $response->header('Content-Type');
            if (! $mime) {
                $f = new \finfo(FILEINFO_MIME_TYPE);
                $mime = $f->buffer($body) ?: 'application/octet-stream';
            }

            $b64 = base64_encode($body);
            return "data:{$mime};base64,{$b64}";
        } catch (\Throwable $e) {
            Log::warning("Failed to fetch remote image {$url}: {$e->getMessage()}");
            return null;
        }
    }
}
