<?php

namespace App\Services;

use App\Models\Photo;
use App\Support\QrCodeGenerator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PhotoWatermarkService
{
    /**
     * Apply Event Logo and Photo QR Code as watermarks to the image.
     */
    public static function apply(Photo $photo): void
    {
        // Watermarking is optional. If the image library isn't installed, we skip without failing uploads.
        if (! extension_loaded('gd')) {
            Log::warning('GD extension missing. Photo watermarking skipped.', ['photo_id' => $photo->id]);
            return;
        }

        if (! class_exists(ImageManager::class) || ! class_exists(Driver::class)) {
            Log::warning('Intervention Image not installed. Photo watermarking skipped.', ['photo_id' => $photo->id]);
            return;
        }

        try {
            $photo->loadMissing('event');

            $manager = new ImageManager(new Driver());
            $disk = Storage::disk('public');
            
            if (!$disk->exists($photo->image_path)) {
                return;
            }

            $imagePath = $disk->path($photo->image_path);
            $img = $manager->read($imagePath);
            
            $width = $img->width();
            $height = $img->height();

            // Padding relative to image size
            $padding = (int)($width * 0.03);

            // 1. Prepare QR Code Watermark (Bottom Right)
            // QR should be around 15% of width
            $qrSize = (int)($width * 0.15);
            if ($qrSize < 120) $qrSize = 120;
            if ($qrSize > 400) $qrSize = 400;

            $qrData = url('/photo/' . $photo->id);
            // We use a slightly larger margin for the QR generation itself to ensure scanability
            $qrPngData = QrCodeGenerator::png($qrData, $qrSize, 5);
            $qrImg = $manager->read($qrPngData);

            // Place QR in bottom right
            $img->place($qrImg, 'bottom-right', $padding, $padding);

            // 2. Prepare Event Logo Watermark (Bottom Left)
            $event = $photo->event;
            if ($event && $event->organizer_logo && $disk->exists($event->organizer_logo)) {
                $logoPath = $disk->path($event->organizer_logo);
                $logoImg = $manager->read($logoPath);
                
                // Scale logo width to around 20% of main image width
                $logoWidthTarget = (int)($width * 0.20);
                $logoImg->scale(width: $logoWidthTarget);
                
                // Place Logo in bottom left
                $img->place($logoImg, 'bottom-left', $padding, $padding);
            }

            // Save the branded image back to its original path
            $img->save($imagePath);

        } catch (\Throwable $e) {
            Log::error('Photo branding failed', [
                'photo_id' => $photo->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
