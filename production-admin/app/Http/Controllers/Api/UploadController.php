<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class UploadController extends Controller
{
    /**
     * Upload image
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
        ]);

        try {
            $image = $request->file('image');
            $filename = Str::random(20) . '.webp'; // Always save as WebP
            
            // Load and optimize image
            $img = Image::read($image);
            
            // Resize if larger than 1920x1080 (maintain aspect ratio)
            $img->scaleDown(height: 1080);
            
            // Encode to WebP with 85% quality
            $optimizedImage = $img->toWebp(85);
            
            // Store in public/uploads directory
            $path = 'uploads/' . $filename;
            Storage::disk('public')->put($path, $optimizedImage);
            
            // Generate thumbnail (300x200)
            $thumbnailFilename = Str::random(20) . '_thumb.webp';
            $thumbnailPath = 'uploads/thumbnails/' . $thumbnailFilename;
            
            $thumbnail = Image::read($image);
            $thumbnail->cover(300, 200);
            $thumbnailOptimized = $thumbnail->toWebp(80);
            Storage::disk('public')->put($thumbnailPath, $thumbnailOptimized);
            
            // Generate full URLs
            $url = asset('storage/' . $path);
            $thumbnailUrl = asset('storage/' . $thumbnailPath);

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $url,
                    'path' => $path,
                    'thumbnail' => $thumbnailUrl,
                    'thumbnail_path' => $thumbnailPath,
                ],
                'message' => 'Image uploaded and optimized successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload multiple images
     */
    public function uploadImages(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        try {
            $uploadedImages = [];

            foreach ($request->file('images') as $image) {
                $filename = Str::random(20) . '.webp';
                
                // Load and optimize image
                $img = Image::read($image);
                
                // Resize if larger than 1920x1080
                $img->scaleDown(height: 1080);
                
                // Encode to WebP with 85% quality
                $optimizedImage = $img->toWebp(85);
                
                // Store in public/uploads directory
                $path = 'uploads/' . $filename;
                Storage::disk('public')->put($path, $optimizedImage);
                
                // Generate thumbnail
                $thumbnailFilename = Str::random(20) . '_thumb.webp';
                $thumbnailPath = 'uploads/thumbnails/' . $thumbnailFilename;
                
                $thumbnail = Image::read($image);
                $thumbnail->cover(300, 200);
                $thumbnailOptimized = $thumbnail->toWebp(80);
                Storage::disk('public')->put($thumbnailPath, $thumbnailOptimized);
                
                $url = asset('storage/' . $path);
                $thumbnailUrl = asset('storage/' . $thumbnailPath);

                $uploadedImages[] = [
                    'url' => $url,
                    'path' => $path,
                    'thumbnail' => $thumbnailUrl,
                    'thumbnail_path' => $thumbnailPath,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $uploadedImages,
                'message' => 'Images uploaded and optimized successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Images upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete image
     */
    public function deleteImage(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            $path = $request->path;
            
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Image deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
