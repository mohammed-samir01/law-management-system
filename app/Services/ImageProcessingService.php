<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageProcessingService
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    // Hero: crop 1200×600 object-top, WebP 85%
    public function processHeroImage(string $relativePath): string
    {
        return $this->cropAndConvert($relativePath, 1200, 600, 'hero', 'top');
    }

    // Logo: scale down max 400×400, keep ratio, WebP 85%
    public function processLogoImage(string $relativePath): string
    {
        return $this->scaleAndConvert($relativePath, 400, 400, 'logos');
    }

    // Team photo: crop square 400×400 object-top, WebP 85%
    public function processTeamPhoto(string $relativePath): string
    {
        return $this->cropAndConvert($relativePath, 400, 400, 'team', 'top');
    }

    private function cropAndConvert(string $relativePath, int $w, int $h, string $dir, string $position = 'center'): string
    {
        $srcPath  = Storage::disk('public')->path($relativePath);
        $newRelative = $dir . '/' . Str::uuid() . '.webp';
        $destPath = Storage::disk('public')->path($newRelative);

        $this->manager
            ->read($srcPath)
            ->cover($w, $h, $position)
            ->toWebp(85)
            ->save($destPath);

        Storage::disk('public')->delete($relativePath);

        return $newRelative;
    }

    private function scaleAndConvert(string $relativePath, int $maxW, int $maxH, string $dir): string
    {
        $srcPath     = Storage::disk('public')->path($relativePath);
        $newRelative = $dir . '/' . Str::uuid() . '.webp';
        $destPath    = Storage::disk('public')->path($newRelative);

        $this->manager
            ->read($srcPath)
            ->scaleDown($maxW, $maxH)
            ->toWebp(85)
            ->save($destPath);

        Storage::disk('public')->delete($relativePath);

        return $newRelative;
    }
}
