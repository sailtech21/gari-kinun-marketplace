<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    /**
     * Upload and optimize an image with multiple sizes
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return array ['original', 'large', 'medium', 'thumbnail']
     */
    public function uploadAndOptimize($file, $directory = 'listings')
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $paths = [];

        // Original (max 1920x1080)
        $originalImage = Image::read($file);
        $originalImage->scaleDown(width: 1920);
        $originalPath = "$directory/$filename";
        Storage::put($originalPath, $originalImage->encode());
        $paths['original'] = Storage::url($originalPath);

        // Large (1200x675)
        $largeImage = Image::read($file);
        $largeImage->scaleDown(width: 1200);
        $largePath = "$directory/large_$filename";
        Storage::put($largePath, $largeImage->encode());
        $paths['large'] = Storage::url($largePath);

        // Medium (800x450)
        $mediumImage = Image::read($file);
        $mediumImage->scaleDown(width: 800);
        $mediumPath = "$directory/medium_$filename";
        Storage::put($mediumPath, $mediumImage->encode());
        $paths['medium'] = Storage::url($mediumPath);

        // Thumbnail (300x200)
        $thumbnailImage = Image::read($file);
        $thumbnailImage->cover(300, 200);
        $thumbnailPath = "$directory/thumb_$filename";
        Storage::put($thumbnailPath, $thumbnailImage->encode());
        $paths['thumbnail'] = Storage::url($thumbnailPath);

        return $paths;
    }

    /**
     * Delete all optimized versions of an image
     * 
     * @param string $imagePath
     * @return bool
     */
    public function deleteOptimizedVersions($imagePath)
    {
        $filename = basename($imagePath);
        $directory = dirname($imagePath);

        Storage::delete([
            "$directory/$filename",
            "$directory/large_$filename",
            "$directory/medium_$filename",
            "$directory/thumb_$filename",
        ]);

        return true;
    }

    /**
     * Get optimized version path
     * 
     * @param string $originalPath
     * @param string $size ('large', 'medium', 'thumbnail')
     * @return string
     */
    public function getOptimizedPath($originalPath, $size = 'medium')
    {
        $filename = basename($originalPath);
        $directory = dirname($originalPath);

        $prefix = match($size) {
            'large' => 'large_',
            'medium' => 'medium_',
            'thumbnail' => 'thumb_',
            default => ''
        };

        return "$directory/{$prefix}$filename";
    }
}
