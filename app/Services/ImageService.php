<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ImageService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Upload and convert any uploaded image to WebP format.
     *
     * @param UploadedFile $file The uploaded file instance
     * @param string $directory Subdirectory under public/ (e.g. 'uploads/settings')
     * @param string|null $prefix Filename prefix (e.g. 'logo', 'favicon')
     * @param int|null $maxWidth Optional max width (proportional scaleDown)
     * @param int|null $maxHeight Optional max height (proportional scaleDown)
     * @param string|null $oldPath Optional old file relative path to delete
     * @param int $quality WebP compression quality (1-100, default 85)
     * @return string Relative path of the stored WebP file (e.g. 'uploads/settings/logo_173184.webp')
     */
    public function uploadWebp(
        UploadedFile $file,
        string $directory = 'uploads',
        ?string $prefix = 'img',
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        ?string $oldPath = null,
        int $quality = 85
    ): string {
        // Read file binary directly to avoid OS-level directory read permission restrictions
        $binary = file_get_contents($file->getRealPath());
        $image = $this->manager->decodeBinary($binary);

        // Optional resizing while preserving aspect ratio
        if ($maxWidth !== null || $maxHeight !== null) {
            $image->scaleDown(width: $maxWidth, height: $maxHeight);
        }

        // Encode image to WebP format
        $encoded = $image->encode(new WebpEncoder(quality: $quality));

        // Format clean filename
        $prefix = preg_replace('/[^a-zA-Z0-9_-]/', '', $prefix ?? 'img');
        $filename = sprintf('%s_%s_%s.webp', $prefix, time(), bin2hex(random_bytes(4)));

        // Ensure target directory exists inside public/
        $cleanDir = trim($directory, '/\\');
        $targetDir = public_path($cleanDir);
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $filename;

        // Save file using file_put_contents for reliable cross-platform writing
        file_put_contents($fullPath, (string) $encoded);

        // Delete old image if provided and exists
        if ($oldPath) {
            $this->delete($oldPath);
        }

        return $cleanDir . '/' . $filename;
    }

    /**
     * Delete an existing image file from public path.
     */
    public function delete(?string $relativePath): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        $fullPath = public_path(ltrim($relativePath, '/\\'));
        if (file_exists($fullPath) && is_file($fullPath)) {
            return @unlink($fullPath);
        }

        return false;
    }
}
