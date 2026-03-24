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
            $event = $photo->event;

            $manager = new ImageManager(new Driver());
            $disk = Storage::disk('public');
            
            if (!$disk->exists($photo->image_path)) {
                return;
            }

            $imagePath = $disk->path($photo->image_path);
            $img = $manager->read($imagePath);
            
            $width = $img->width();
            $height = $img->height();

            // Sizing constants relative to image width for consistent look across resolutions
            $padding = (int)($width * 0.025);
            $fontPath = base_path('vendor/endroid/qr-code/assets/open_sans.ttf');
            $hasFont = file_exists($fontPath);

            // --- 1. LEFT BRANDING (Black Box) ---
            // Box width set to 26% of photo width
            $leftBoxWidth = (int)($width * 0.26);
            $leftBoxHeight = (int)($leftBoxWidth * 0.32);
            $leftBox = $manager->create($leftBoxWidth, $leftBoxHeight)->fill('000000');
            
            if ($hasFont) {
                // Title: ROYAL EVENTS (Using slightly larger font for 'ROYAL')
                $leftBox->text('ROYAL EVENTS', (int)($leftBoxWidth * 0.1), (int)($leftBoxHeight * 0.42), function($font) use ($fontPath, $leftBoxHeight) {
                    $font->file($fontPath);
                    $font->size((int)($leftBoxHeight * 0.38));
                    $font->color('ffffff');
                    $font->align('left');
                    $font->valign('middle');
                });
                // Tagline: MOMENTS, MADE MEANINGFUL (Elegant and subtle)
                $leftBox->text('MOMENTS, MADE MEANINGFUL', (int)($leftBoxWidth * 0.1), (int)($leftBoxHeight * 0.78), function($font) use ($fontPath, $leftBoxHeight) {
                    $font->file($fontPath);
                    $font->size((int)($leftBoxHeight * 0.16));
                    $font->color('bbbbbb'); // Slightly lighter gray for better contrast on black
                    $font->align('left');
                    $font->valign('middle');
                });
            }
            $img->place($leftBox, 'bottom-left', $padding, $padding);

            // --- 2. RIGHT BRANDING (White Box with QR & Text) ---
            // Box width set to 30% of photo width to accommodate initials and QR
            $rightBoxWidth = (int)($width * 0.30);
            $rightBoxHeight = (int)($rightBoxWidth * 0.36);
            $rightBox = $manager->create($rightBoxWidth, $rightBoxHeight)->fill('ffffff');
            
            // A. QR Code (Left side of white box)
            $qrPadding = (int)($rightBoxHeight * 0.12);
            $qrHeight = $rightBoxHeight - ($qrPadding * 2);
            $qrData = url('/photo/' . $photo->id);
            // We use 0 margin for the QR inside our box to maximize size
            $qrPngData = QrCodeGenerator::png($qrData, $qrHeight, 0);
            $qrImg = $manager->read($qrPngData);
            $rightBox->place($qrImg, 'left', (int)($rightBoxWidth * 0.04), 0);
            
            // B. Text Details (Right side of white box)
            if ($hasFont && $event) {
                $textX = (int)($rightBoxWidth * 0.06 + $qrHeight + $rightBoxWidth * 0.04);
                
                // Optimized Initials Extraction (e.g., "Rahul & Priya" -> "RP")
                $initials = '';
                if ($event->couple_name) {
                    $parts = preg_split('/\s+(&|and|with)\s+/i', $event->couple_name);
                    foreach ($parts as $part) {
                        $initials .= strtoupper(substr(trim($part), 0, 1));
                    }
                }
                if (empty($initials)) $initials = strtoupper(substr($event->event_name ?? 'EV', 0, 2));
                if (strlen($initials) > 2) $initials = substr($initials, 0, 2);

                // Render Initials (Large and prominent)
                $rightBox->text($initials, $textX, (int)($rightBoxHeight * 0.42), function($font) use ($fontPath, $rightBoxHeight) {
                    $font->file($fontPath);
                    $font->size((int)($rightBoxHeight * 0.50));
                    $font->color('000000');
                    $font->align('left');
                    $font->valign('middle');
                });
                
                // Render Event Name (Smaller elegant label)
                $eventName = strtoupper($event->event_name ?? 'EVENT GALLERY');
                $rightBox->text($eventName, $textX, (int)($rightBoxHeight * 0.82), function($font) use ($fontPath, $rightBoxHeight) {
                    $font->file($fontPath);
                    $font->size((int)($rightBoxHeight * 0.14));
                    $font->color('444444');
                    $font->align('left');
                    $font->valign('middle');
                });
            }
            $img->place($rightBox, 'bottom-right', $padding, $padding);

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
