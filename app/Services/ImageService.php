<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;

class ImageService
{
    /**
     * Maximum width for resized images
     */
    const MAX_WIDTH = 800;

    /**
     * Maximum height for resized images
     */
    const MAX_HEIGHT = 600;

    /**
     * JPEG quality (1-100, higher = better quality but larger file)
     */
    const JPEG_QUALITY = 85;

    /**
     * Get ImageManager instance
     */
    private static function manager(): ImageManager
    {
        return new ImageManager(new GdDriver());
    }

    /**
     * Process and store an uploaded image
     *
     * @param UploadedFile $file The uploaded image file
     * @param string $folder The storage folder (e.g., 'courses', 'subjects', 'students', 'users')
     * @return string The stored file path
     */
    public static function store(UploadedFile $file, string $folder): string
    {
        // Generate unique filename with original extension
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $folder . '/' . uniqid() . '.' . $extension;

        // Create image manager and read the uploaded file
        $manager = self::manager();
        $img = $manager->read($file->getRealPath());

        // Resize maintaining aspect ratio (max width 800px, max height 600px)
        $img->scaleDown(self::MAX_WIDTH, self::MAX_HEIGHT);

        // Optimize based on format
        if ($extension === 'png') {
            // PNG: keep as PNG but optimize
            $encoded = $img->encode(new PngEncoder());
        } elseif (in_array($extension, ['jpg', 'jpeg'])) {
            // JPEG: optimize with quality setting
            $encoded = $img->encode(new JpegEncoder(self::JPEG_QUALITY));
            // Update filename to .jpg if needed
            if ($extension !== 'jpg') {
                $filename = str_replace('.' . $extension, '.jpg', $filename);
            }
        } else {
            // Other formats: keep original encoding (auto-detect)
            $encoded = $img->encode();
        }

        // Store the optimized image
        Storage::disk('public')->put($filename, $encoded->toString());

        return $filename;
    }

    /**
     * Delete an image file
     *
     * @param string|null $path The file path to delete
     * @return bool True if deleted, false otherwise
     */
    public static function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
