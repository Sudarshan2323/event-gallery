<?php
require __DIR__ . '/vendor/autoload.php';
echo "ImageManager exists: " . (class_exists('Intervention\Image\ImageManager') ? 'Yes' : 'No') . "\n";
echo "Gd Driver exists: " . (class_exists('Intervention\Image\Drivers\Gd\Driver') ? 'Yes' : 'No') . "\n";
if (class_exists('Intervention\Image\ImageManager')) {
    try {
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        echo "Successfully instantiated ImageManager with Gd Driver\n";
    } catch (\Throwable $e) {
        echo "Instantiation failed: " . $e->getMessage() . "\n";
    }
}
