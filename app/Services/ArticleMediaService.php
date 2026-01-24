<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleMediaService
{
    /**
     * Upload and process an image (max 2MB)
     */
    public function uploadImage(UploadedFile $file, ?Article $article = null, $userId = null): array
    {
        // Validate file size (2MB max)
        if ($file->getSize() > 2048 * 1024) {
            throw new \Exception('Image size must not exceed 2MB');
        }
        
        // Validate file type
        if (!in_array($file->extension(), ['jpg', 'jpeg', 'png', 'gif'])) {
            throw new \Exception('Only JPG, PNG, and GIF images are allowed');
        }
        
        // Process image
        $processedPath = $this->processImage($file);
        
        // Store media record
        $media = ArticleMedia::create([
            'article_id' => $article ? $article->id : null,
            'user_id' => $userId ?? auth()->id(),
            'filename' => basename($processedPath),
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $processedPath,
            'mime_type' => $file->getMimeType(),
            'file_size' => Storage::disk('public')->size($processedPath),
        ]);
        
        return [
            'success' => true,
            'location' => asset('storage/' . $processedPath),
            'media_id' => $media->id,
        ];
    }

    /**
     * Process and optimize image using native PHP GD
     */
    public function processImage(UploadedFile $file): string
    {
        $filename = Str::random(40) . '.jpg';
        $path = 'articles/images/' . date('Y/m');
        $fullPath = $path . '/' . $filename;
        
        // Get full storage path for directory
        $directoryPath = storage_path('app/public/' . $path);
        
        // Create directory with proper permissions if it doesn't exist
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }
        
        // Get full file path
        $storagePath = storage_path('app/public/' . $fullPath);
        
        // Create image resource based on mime type
        $mimeType = $file->getMimeType();
        $sourceImage = match($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($file->getPathname()),
            'image/png' => imagecreatefrompng($file->getPathname()),
            'image/gif' => imagecreatefromgif($file->getPathname()),
            default => throw new \Exception('Unsupported image type'),
        };
        
        if (!$sourceImage) {
            throw new \Exception('Failed to process image');
        }
        
        // Get original dimensions
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);
        
        // Calculate new dimensions (max 1200px width)
        $maxWidth = 1200;
        if ($originalWidth > $maxWidth) {
            $ratio = $maxWidth / $originalWidth;
            $newWidth = $maxWidth;
            $newHeight = (int)($originalHeight * $ratio);
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }
        
        // Create resized image
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        
        // Resize
        imagecopyresampled(
            $resizedImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );
        
        // Compress and save - start with quality 85
        $quality = 85;
        $tempPath = $storagePath;
        
        // Try to save under 1MB by reducing quality
        do {
            imagejpeg($resizedImage, $tempPath, $quality);
            $fileSize = filesize($tempPath);
            
            if ($fileSize > 1024 * 1024) { // 1MB
                $quality -= 5;
            }
        } while ($fileSize > 1024 * 1024 && $quality >= 50);
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
        
        return $fullPath;
    }

    /**
     * Delete media and its file
     */
    public function deleteMedia(int $mediaId): bool
    {
        $media = ArticleMedia::find($mediaId);
        
        if (!$media) {
            return false;
        }
        
        // Delete file from storage
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }
        
        // Delete record
        $media->delete();
        
        return true;
    }

    /**
     * Parse and embed video URL
     */
    public function embedVideo(string $url): ?string
    {
        // YouTube patterns
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
            return '<div class="video-container"><iframe width="100%" height="400" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
        }
        
        // Vimeo pattern
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            $videoId = $matches[1];
            return '<div class="video-container"><iframe width="100%" height="400" src="https://player.vimeo.com/video/' . $videoId . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe></div>';
        }
        
        return null;
    }

    /**
     * Get video thumbnail from URL
     */
    public function getVideoThumbnail(string $url): ?string
    {
        // YouTube thumbnail
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return "https://img.youtube.com/vi/{$matches[1]}/maxresdefault.jpg";
        }
        
        return null;
    }
}
