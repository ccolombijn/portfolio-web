<?php

namespace App\Services;

use Spatie\Image\Image;
use Spatie\ImageOptimizer\OptimizerChain; 
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileProcessingService
{
    /**
     * 
     * @var \Spatie\ImageOptimizer\OptimizerChain
     */
    protected OptimizerChain $optimizer;

    public function __construct()
    {
        $this->optimizer = OptimizerChainFactory::create();
    }

    /**
     * Recursively gets the size of a directory.
     */
    public function getDirectorySize(string $path): int
    {
        if (!is_dir($path)) {
            return 0;
        }
        
        $size = 0;
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
        foreach ($files as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    /**
     * Formats a file size in bytes to a human-readable string.
     */
    public function formatFileSize(int $bytes, int $decimals = 2): string
    {
        if ($bytes <= 0) return '0 B';
        $factor = floor((strlen($bytes) - 1) / 3);
        $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $sizes[$factor];
    }

    /**
     * Stores an uploaded file, optimizes it, and creates a WebP version.
     */
    public function optimizeAndConvert(UploadedFile $file, string $savePath): void
    {
        $savedPath = $file->storeAs(dirname($savePath), basename($savePath), 'public');
        $physicalPath = Storage::disk('public')->path($savedPath);

        $this->optimizer->optimize($physicalPath);

        $extension = strtolower(pathinfo($physicalPath, PATHINFO_EXTENSION));
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $webpPath = pathinfo($physicalPath, PATHINFO_DIRNAME) . '/' . pathinfo($physicalPath, PATHINFO_FILENAME) . '.webp';
            Image::load($physicalPath)->save($webpPath);
        }
    }
}