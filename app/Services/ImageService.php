<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Upload an image with security validation
     *
     * @param UploadedFile $file
     * @param string $path
     * @return string The storage path
     * @throws \Exception
     */
    public function uploadImage(UploadedFile $file, string $path = 'listings'): string
    {
        // Validate the image
        $this->validateImage($file);

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);

        // Store the file
        $storedPath = $file->storeAs($path, $filename, 'public');

        if (!$storedPath) {
            throw new \Exception('Failed to store image file.');
        }

        Log::info('Image uploaded successfully', [
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'size' => $file->getSize(),
        ]);

        return '/storage/' . $storedPath;
    }

    /**
     * Delete an image from storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteImage(string $path): bool
    {
        // Validate path to prevent directory traversal
        if (!$this->isValidPath($path)) {
            Log::warning('Attempted to delete invalid path', ['path' => $path]);
            return false;
        }

        // Remove /storage/ prefix if present
        $cleanPath = str_replace('/storage/', '', $path);

        // Check if file exists
        if (!Storage::disk('public')->exists($cleanPath)) {
            Log::warning('Attempted to delete non-existent file', ['path' => $cleanPath]);
            return false;
        }

        // Delete the file
        $deleted = Storage::disk('public')->delete($cleanPath);

        if ($deleted) {
            Log::info('Image deleted successfully', ['path' => $cleanPath]);
        } else {
            Log::error('Failed to delete image', ['path' => $cleanPath]);
        }

        return $deleted;
    }

    /**
     * Validate an uploaded image
     *
     * @param UploadedFile $file
     * @return bool
     * @throws \Exception
     */
    public function validateImage(UploadedFile $file): bool
    {
        // Check if file is valid
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload.');
        }

        // Validate MIME type (not just extension)
        $allowedMimes = config('listing.images.allowed_mimes', [
            'image/jpeg',
            'image/png',
            'image/webp'
        ]);

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedMimes)) {
            throw new \Exception("Invalid image type. Allowed types: " . implode(', ', $allowedMimes));
        }

        // Validate file size
        $maxSize = config('listing.images.max_size', 5120) * 1024; // Convert KB to bytes
        if ($file->getSize() > $maxSize) {
            throw new \Exception("Image size exceeds maximum allowed size of " . ($maxSize / 1024) . "KB.");
        }

        // Validate image dimensions
        $imageInfo = @getimagesize($file->getRealPath());
        if ($imageInfo === false) {
            throw new \Exception('Invalid image file.');
        }

        [$width, $height] = $imageInfo;

        $minDimensions = config('listing.images.min_dimensions', ['width' => 200, 'height' => 200]);
        $maxDimensions = config('listing.images.max_dimensions', ['width' => 4096, 'height' => 4096]);

        if ($width < $minDimensions['width'] || $height < $minDimensions['height']) {
            throw new \Exception("Image dimensions too small. Minimum: {$minDimensions['width']}x{$minDimensions['height']}px.");
        }

        if ($width > $maxDimensions['width'] || $height > $maxDimensions['height']) {
            throw new \Exception("Image dimensions too large. Maximum: {$maxDimensions['width']}x{$maxDimensions['height']}px.");
        }

        // Additional security: Check for PHP code in image
        $fileContent = file_get_contents($file->getRealPath());
        if ($this->containsPhpCode($fileContent)) {
            Log::warning('Attempted to upload image containing PHP code', [
                'filename' => $file->getClientOriginalName(),
                'mime' => $mimeType,
            ]);
            throw new \Exception('Invalid image file content.');
        }

        return true;
    }

    /**
     * Generate a unique filename for the uploaded file
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        
        // Validate extension
        $allowedExtensions = config('listing.images.allowed_extensions', ['jpg', 'jpeg', 'png', 'webp']);
        if (!in_array(strtolower($extension), $allowedExtensions)) {
            $extension = 'jpg'; // Default to jpg if invalid
        }

        // Generate unique name
        return Str::random(40) . '.' . $extension;
    }

    /**
     * Validate that a path is safe (prevent directory traversal)
     *
     * @param string $path
     * @return bool
     */
    protected function isValidPath(string $path): bool
    {
        // Check for directory traversal attempts
        if (str_contains($path, '..') || str_contains($path, '//')) {
            return false;
        }

        // Check for null bytes
        if (str_contains($path, "\0")) {
            return false;
        }

        // Path should start with /storage/ or be a relative path
        if (!str_starts_with($path, '/storage/') && !str_starts_with($path, 'listings/')) {
            return false;
        }

        return true;
    }

    /**
     * Check if file content contains PHP code
     *
     * @param string $content
     * @return bool
     */
    protected function containsPhpCode(string $content): bool
    {
        // Check for PHP opening tags
        $phpPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<\?/i',
            '/<script.*language.*php.*>/i',
        ];

        foreach ($phpPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get image info
     *
     * @param string $path
     * @return array|null
     */
    public function getImageInfo(string $path): ?array
    {
        $cleanPath = str_replace('/storage/', '', $path);
        $fullPath = Storage::disk('public')->path($cleanPath);

        if (!file_exists($fullPath)) {
            return null;
        }

        $imageInfo = @getimagesize($fullPath);
        if ($imageInfo === false) {
            return null;
        }

        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'mime' => $imageInfo['mime'],
            'size' => filesize($fullPath),
        ];
    }

    /**
     * Batch delete images
     *
     * @param array $paths
     * @return int Number of successfully deleted images
     */
    public function batchDeleteImages(array $paths): int
    {
        $deleted = 0;

        foreach ($paths as $path) {
            if ($this->deleteImage($path)) {
                $deleted++;
            }
        }

        return $deleted;
    }
}
