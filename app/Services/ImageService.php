<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;

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
     * Maximum width for table thumbnails.
     */
    const TABLE_MAX_WIDTH = 160;

    /**
     * Maximum height for table thumbnails.
     */
    const TABLE_MAX_HEIGHT = 160;

    /**
     * JPEG quality for table thumbnails.
     */
    const TABLE_JPEG_QUALITY = 72;

    /**
     * Get ImageManager instance
     */
    private static function manager(): ImageManager
    {
        return new ImageManager(new GdDriver);
    }

    /**
     * Process and store an uploaded image
     *
     * @param  UploadedFile  $file  The uploaded image file
     * @param  string  $folder  The storage folder (e.g., 'courses', 'subjects', 'students', 'users')
     * @return string The stored file path
     */
    public static function store(UploadedFile $file, string $folder): string
    {
        // Generate unique filename with original extension
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $folder.'/'.uniqid().'.'.$extension;

        $filename = self::storeOptimizedVariant(
            $file->getRealPath(),
            $filename,
            self::MAX_WIDTH,
            self::MAX_HEIGHT,
            self::JPEG_QUALITY
        );

        self::storeOptimizedVariant(
            $file->getRealPath(),
            self::tableVariantPath($filename),
            self::TABLE_MAX_WIDTH,
            self::TABLE_MAX_HEIGHT,
            self::TABLE_JPEG_QUALITY
        );

        return $filename;
    }

    /**
     * Get the table thumbnail path, falling back to the original image when needed.
     */
    public static function tablePath(?string $path): ?string
    {
        $normalizedPath = self::normalizePath($path);
        if ($normalizedPath === null) {
            return null;
        }

        $disk = Storage::disk('public');
        $tablePath = self::tableVariantPath($normalizedPath);

        if ($disk->exists($tablePath)) {
            return $tablePath;
        }

        return $normalizedPath;
    }

    /**
     * Delete an image file
     *
     * @param  string|null  $path  The file path to delete
     * @return bool True if deleted, false otherwise
     */
    public static function delete(?string $path): bool
    {
        $normalizedPath = self::normalizePath($path);
        if ($normalizedPath === null) {
            return false;
        }

        $disk = Storage::disk('public');
        $paths = array_values(array_unique([
            $normalizedPath,
            self::tableVariantPath($normalizedPath),
        ]));

        if (! collect($paths)->contains(fn (string $candidate) => $disk->exists($candidate))) {
            return false;
        }

        foreach ($paths as $candidate) {
            if (! $disk->exists($candidate)) {
                continue;
            }

            $disk->delete($candidate);

            if (! $disk->exists($candidate)) {
                continue;
            }

            // Fallback for local/public disk cases where adapter delete can be flaky.
            try {
                $absolutePath = $disk->path($candidate);
                if (is_file($absolutePath)) {
                    @unlink($absolutePath);
                }
            } catch (\Throwable) {
                // Ignore and report based on current disk state below.
            }
        }

        return ! collect($paths)->contains(fn (string $candidate) => $disk->exists($candidate));
    }

    private static function storeOptimizedVariant(
        string $sourcePath,
        string $targetPath,
        int $maxWidth,
        int $maxHeight,
        int $jpegQuality
    ): string {
        $manager = self::manager();
        $img = $manager->read($sourcePath);
        $img->scaleDown($maxWidth, $maxHeight);

        $extension = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

        if ($extension === 'png') {
            $encoded = $img->encode(new PngEncoder);
        } elseif (in_array($extension, ['jpg', 'jpeg'], true)) {
            $encoded = $img->encode(new JpegEncoder($jpegQuality));
            if ($extension !== 'jpg') {
                $targetPath = preg_replace('/\.(jpe?g)$/i', '.jpg', $targetPath) ?? $targetPath;
            }
        } else {
            $encoded = $img->encode();
        }

        Storage::disk('public')->put($targetPath, $encoded->toString());

        return $targetPath;
    }

    private static function normalizePath(?string $path): ?string
    {
        $normalizedPath = trim((string) $path);
        if ($normalizedPath === '') {
            return null;
        }

        return ltrim(str_replace('\\', '/', $normalizedPath), '/');
    }

    private static function tableVariantPath(string $path): string
    {
        $directory = trim((string) pathinfo($path, PATHINFO_DIRNAME), './\\');
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        $segments = [];
        if ($directory !== '' && $directory !== '.') {
            $segments[] = $directory;
        }
        $segments[] = 'table';
        $segments[] = $filename.($extension !== '' ? '.'.$extension : '');

        return implode('/', $segments);
    }
}
