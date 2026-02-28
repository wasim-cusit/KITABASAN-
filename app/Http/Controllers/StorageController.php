<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves files from the public storage disk.
 * Route: GET /files/{path} — request always hits Laravel so images work on admin and everywhere.
 */
class StorageController extends Controller
{
    /**
     * Serve a file from storage/app/public by path.
     * URL: /files/{path} e.g. /files/profiles/abc.jpg
     */
    public function show(string $path): StreamedResponse
    {
        $path = rawurldecode($path);
        $path = ltrim(str_replace('\\', '/', $path), '/');
        if (preg_match('#\.\./#', $path) || preg_match('#/\.\.#', $path)) {
            abort(404);
        }

        $fullPath = storage_path('app/public/' . $path);

        if (! File::isFile($fullPath)) {
            abort(404);
        }

        $mime = File::mimeType($fullPath) ?: 'application/octet-stream';
        $stream = function () use ($fullPath): void {
            $handle = fopen($fullPath, 'rb');
            if ($handle) {
                while (! feof($handle)) {
                    echo fread($handle, 8192);
                    if (ob_get_level()) {
                        ob_flush();
                    }
                    flush();
                }
                fclose($handle);
            }
        };

        return response()->stream($stream, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
