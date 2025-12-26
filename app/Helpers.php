<?php

namespace App;

use Illuminate\Support\Facades\Storage;

if (! function_exists('imageRecover')) {
    function imageRecover($path)
    {
        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        if ($path == null || ! Storage::disk(env('FILESYSTEM_DISK'))->exists($path)) {
            return asset('assets/placeholder.png');
        }

        return asset(Storage::url($path));
    }
}

if (! function_exists('getFileUrl')) {
    function getFileUrl($path)
    {
        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        if ($path == null || ! Storage::disk(env('FILESYSTEM_DISK'))->exists($path)) {
            return null;
        }

        return asset(Storage::url($path));
    }
}
