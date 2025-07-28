<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ImageUploadService
{
    public function upload(UploadedFile $file, string $path): string
    {
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($path), $filename);
        return $path . '/' . $filename;
    }

    public function deleteIfExists(?string $filepath): void
    {
        $fullPath = public_path($filepath);
        if ($filepath && file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
