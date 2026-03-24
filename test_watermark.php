<?php

require __DIR__ . '/vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Support\QrCodeGenerator;

class WatermarkTester {
    public static function test() {
        $manager = new ImageManager(new Driver());
        
        // 1. Create a dummy photo (1200x800)
        $img = $manager->create(1200, 800)->fill('dddddd');
        $width = $img->width();
        
        // Settings
        $padding = (int)($width * 0.03);
        
        // 2. Right watermark box size
        $boxWidth = (int)($width * 0.25); // 25% of image width
        $boxHeight = (int)($boxWidth * 1.5); // Taller than wide to hold monogram + QR
        if ($boxWidth < 200) { $boxWidth = 200; $boxHeight = 300; }
        
        // Create the white box
        $box = $manager->create($boxWidth, $boxHeight)->fill('ffffff');
        
        // 3. Draw monogram in the top part of the box
        // We'll draw a circle first, then text inside it
        $circleRadius = (int)($boxWidth * 0.4);
        $circleX = (int)($boxWidth / 2);
        $circleY = (int)($boxHeight * 0.35);
        
        $box->drawCircle($circleX, $circleY, function ($circle) use ($circleRadius) {
            $circle->radius($circleRadius);
            $circle->border('800000', 3); // Maroon border
        });
        
        // Draw initials M | G
        $fontPath = __DIR__ . '/storage/app/public/fonts/PlayfairDisplay-Regular.ttf';
        if (file_exists($fontPath)) {
            $fontSize = (int)($circleRadius * 0.5);
            $box->text('M | G', $circleX, $circleY, function ($font) use ($fontPath, $fontSize) {
                $font->file($fontPath);
                $font->size($fontSize);
                $font->color('800000'); // Maroon color
                $font->align('center');
                $font->valign('middle');
            });
        }
        
        // 4. Generate QR Code for the bottom part of the box
        // The QR code data
        $qrData = 'https://example.com/photo/123';
        
        // The QR code size should be the width of the box, minus padding
        $qrPadding = 20;
        $qrSize = $boxWidth - ($qrPadding * 2);
        
        // Generate QR Code PNG string
        $qrPngData = QrCodeGenerator::png($qrData, $qrSize, 0); // Margin handled by box
        $qrImg = $manager->read($qrPngData);
        
        // Place QR code at the bottom of the white box
        $box->place($qrImg, 'bottom-center', 0, $qrPadding);
        
        // 5. Place the combined box on the main image (bottom right)
        $img->place($box, 'bottom-right', $padding, $padding);
        
        // 6. Draw Organizer logo in bottom left
        // Let's create a dummy logo
        $logoWidthTarget = (int)($width * 0.20);
        $logo = $manager->create($logoWidthTarget, (int)($logoWidthTarget * 0.4))->fill('000000');
        $logo->text('The Shubh Events', $logoWidthTarget / 2, ($logoWidthTarget * 0.4) / 2, function($font) use ($fontPath) {
             $font->file($fontPath);
             $font->size(20);
             $font->color('ffffff');
             $font->align('center');
             $font->valign('middle');
        });
        
        $img->place($logo, 'bottom-left', $padding, $padding);
        
        // Save
        $outputPath = __DIR__ . '/public/test_watermark_output.jpg';
        $img->save($outputPath);
        echo "Saved to $outputPath\n";
    }
}

WatermarkTester::test();
