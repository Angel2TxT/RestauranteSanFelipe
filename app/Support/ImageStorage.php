<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

class ImageStorage
{
    /**
     * Guarda una imagen en public/images/{folder} y devuelve la ruta relativa.
     */
    public static function store(UploadedFile $file, string $folder, ?string $previousPath = null): string
    {
        $directory = public_path("images/{$folder}");

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if ($previousPath) {
            self::delete($previousPath);
        }

        $filename = uniqid('', true) . '.' . ($file->guessExtension() ?: 'jpg');
        $file->move($directory, $filename);

        return "images/{$folder}/{$filename}";
    }

    public static function delete(?string $path): void
    {
        if (!$path || str_contains($path, 'no-image') || str_contains($path, 'image.jpg')) {
            return;
        }

        $fullPath = public_path($path);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
