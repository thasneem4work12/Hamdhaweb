<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    public function processProductImage(UploadedFile $file): array
    {
        $uuid = Str::uuid();

        $image = Image::read($file);
        $image->scaleDown(width: 1080, height: 1350);
        $fullPath = "products/{$uuid}.webp";
        $image->toWebp(quality: 85)
            ->save(storage_path("app/public/{$fullPath}"));

        $thumb = Image::read($file);
        $thumb->scaleDown(width: 540, height: 675);
        $thumbPath = "products/thumbs/{$uuid}.webp";
        $thumb->toWebp(quality: 80)
            ->save(storage_path("app/public/{$thumbPath}"));

        return [
            'image_path' => $fullPath,
            'thumbnail_path' => $thumbPath,
        ];
    }

    public function processCategoryImage(UploadedFile $file): string
    {
        $uuid = Str::uuid();
        $image = Image::read($file);
        $image->scaleDown(width: 1080, height: 1350);
        $path = "categories/{$uuid}.webp";
        $image->toWebp(quality: 85)
            ->save(storage_path("app/public/{$path}"));

        return $path;
    }

    public function processSizeChartImage(UploadedFile $file): string
    {
        $uuid = Str::uuid();
        $image = Image::read($file);
        $image->scaleDown(width: 1080, height: 1350);
        $path = "size-charts/{$uuid}.webp";
        $image->toWebp(quality: 85)
            ->save(storage_path("app/public/{$path}"));

        return $path;
    }

    public function processHomepageImage(UploadedFile|string $file): string
    {
        $uuid = Str::uuid();

        if ($file instanceof UploadedFile) {
            $image = Image::read($file);
        } else {
            $fullPath = storage_path("app/public/{$file}");

            if (! file_exists($fullPath)) {
                return $file;
            }

            $image = Image::read($fullPath);
        }

        $newPath = "homepage/{$uuid}.webp";
        $image->scaleDown(width: 1920);
        $image->toWebp(quality: 85)
            ->save(storage_path("app/public/{$newPath}"));

        if (is_string($file)) {
            $originalFullPath = storage_path("app/public/{$file}");
            if (file_exists($originalFullPath) && $originalFullPath !== storage_path("app/public/{$newPath}")) {
                unlink($originalFullPath);
            }
        }

        return $newPath;
    }

    public function deleteImage(string $path): void
    {
        $fullPath = storage_path("app/public/{$path}");
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
