<?php

namespace App\Service;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class FileHandlerService
{
    private function generateFileName($file)
    {
        return time() . "_" . uniqid() . "_" . $file->getClientOriginalName();
    }

    private function ensureDirectoryExists($path, $disk = 'public')
    {
        if (!Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->makeDirectory($path, 0755, true);
        }
    }

    private function resizeAndSaveImage($file, $storingPath, $width = null, $height = null)
    {
        $image = Image::make($file->getRealPath());

        if ($width || $height) {
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $image->save($storingPath);
    }

    public function uploader($file, $path, $width = null, $height = null)
    {
        if (strtolower($file->getClientOriginalExtension()) == 'svg') {
            return $this->fileUploadAndGetPath($file, $path);
        }

        $fileName = $this->generateFileName($file);
        $storagePath = storage_path("app{$path}/{$fileName}");

        $this->ensureDirectoryExists($path, 'public');
        $this->resizeAndSaveImage($file, $storagePath, $width, $height);

        return str_replace('/public', '', "{$path}/{$fileName}");
    }

    public function uploadFileToPublic($file, $path = "/assets/images", $height = 400)
    {
        $fileName = $this->generateFileName($file);
        $storagePath = public_path("{$path}/{$fileName}");

        $this->ensureDirectoryExists($path, 'public');
        $this->resizeAndSaveImage($file, $storagePath, null, $height);

        return "{$path}/{$fileName}";
    }

    public function deleteImageFromPublic($path)
    {
        $absolutePath = public_path($path);

        if (File::exists($absolutePath) && File::isFile($absolutePath)) {
            return File::delete($absolutePath);
        }

        return false;
    }

    public function uploadIconAndGetPath($file, $path = "/public/media/others")
    {
        return $this->uploader($file, $path, null, 200);
    }

    public function uploadImageAndGetPath($file, $path = "/public/media/others")
    {
        return $this->uploader($file, $path, null, 400);
    }

    public function uploadBigImageAndGetPath($file, $path = "/public/media/others")
    {
        return $this->uploader($file, $path, 800, null);
    }

    public function deleteImage($path)
    {
        $absolutePath = storage_path("app/public/{$path}");

        if (File::exists($absolutePath) && File::isFile($absolutePath)) {
            return File::delete($absolutePath);
        }

        return false;
    }

    public function fileUploadAndGetPath($file, $path = "/public/media/others")
    {
        $fileName = $this->generateFileName($file);

        $this->ensureDirectoryExists($path, 'public');
        $file->storeAs($path, $fileName);

        return str_replace('/public/', '', "{$path}/{$fileName}");
    }
}
