<?php

require __DIR__ . '/vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Support\QrCodeGenerator;

class WatermarkTesterV2 {
    public static function test() {
        $manager = new ImageManager(new Driver());
        
        // 1. Create a dummy photo (1200x800)
        $img = $manager->create(1200, 800)->fill('dddddd');
        $width = $img->width();
        
        // Settings
        $padding = (int)($width * 0.03);
        $fontPath = __DIR__ . '/storage/app/public/fonts/Inter-Variable.ttf';
        
        // --- LEFT BRANDING (Black Box) ---
        $leftBoxWidth = (int)($width * 0.22);
        $leftBoxHeight = (int)($leftBoxWidth * 0.35);
        $leftBox = $manager->create($leftBoxWidth, $leftBoxHeight)->fill('000000');
        
        if (file_exists($fontPath)) {
            // "ROYAL EVENTS"
            $leftBox->text('ROYAL EVENTS', (int)($leftBoxWidth * 0.05), (int)($leftBoxHeight * 0.45), function($font) use ($fontPath, $leftBoxHeight) {
                $font->file($fontPath);
                $font->size((int)($leftBoxHeight * 0.35));
                $font->color('ffffff');
                $font->align('left');
                $font->valign('middle');
            });
            // "MOMENTS, MADE MEANINGFUL"
            $leftBox->text('MOMENTS, MADE MEANINGFUL', (int)($leftBoxWidth * 0.05), (int)($leftBoxHeight * 0.75), function($font) use ($fontPath, $leftBoxHeight) {
                $font->file($fontPath);
                $font->size((int)($leftBoxHeight * 0.18));
                $font->color('cccccc');
                $font->align('left');
                $font->valign('middle');
            });
        }
        
        $img->place($leftBox, 'bottom-left', $padding, $padding);
        
        // --- RIGHT BRANDING (White Box with QR & Text) ---
        $rightBoxWidth = (int)($width * 0.25);
        $rightBoxHeight = (int)($rightBoxWidth * 0.35);
        $rightBox = $manager->create($rightBoxWidth, $rightBoxHeight)->fill('ffffff');
        
        // 1. QR Code (Left side of white box)
        $qrPadding = (int)($rightBoxHeight * 0.1);
        $qrSize = $rightBoxHeight - ($qrPadding * 2);
        $qrData = 'https://example.com/photo/123';
        $qrPngData = QrCodeGenerator::png($qrData, $qrSize, 0);
        $qrImg = $manager->read($qrPngData);
        $rightBox->place($qrImg, 'left', (int)($rightBoxWidth * 0.03), 0);
        
        // 2. Text (Right side of white box)
        if (file_exists($fontPath)) {
            $textX = (int)($rightBoxWidth * 0.03 + $qrSize + $rightBoxWidth * 0.05);
            
            // Initials "RP"
            $rightBox->text('RP', $textX, (int)($rightBoxHeight * 0.4), function($font) use ($fontPath, $rightBoxHeight) {
                $font->file($fontPath);
                $font->size((int)($rightBoxHeight * 0.45));
                $font->color('000000');
                $font->align('left');
                $font->valign('middle');
            });
            
            // Event Name "RAHUL & PRIYA WEDDING"
            $rightBox->text('RAHUL & PRIYA WEDDING', $textX, (int)($rightBoxHeight * 0.75), function($font) use ($fontPath, $rightBoxHeight) {
                $font->file($fontPath);
                $font->size((int)($rightBoxHeight * 0.15));
                $font->color('333333');
                $font->align('left');
                $font->valign('middle');
            });
        }
        
        $img->place($rightBox, 'bottom-right', $padding, $padding);
        
        // Save
        $outputPath = __DIR__ . '/public/test_watermark_v2.jpg';
        $img->save($outputPath);
        echo "Saved to $outputPath\n";
    }
}

WatermarkTesterV2::test();
