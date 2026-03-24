<?php

use App\Models\Event;
use App\Models\Photo;
use App\Services\PhotoWatermarkService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Create a dummy event
$event = Event::firstOrNew(['event_name' => 'Rahul & Priya Wedding']);
$event->couple_name = 'Rahul & Priya';
$event->slug = Str::slug($event->event_name);
$event->save();

// 2. Create a dummy photo
$photo = new Photo();
$photo->event_id = $event->id;
$photo->uuid = (string) Str::uuid();
$photo->image_path = 'photos/test_branded.jpg';
$photo->save();

// 3. Create a dummy image file (1200x800)
$disk = Storage::disk('public');
$disk->makeDirectory('photos');
$imagePath = $disk->path($photo->image_path);

$manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
$img = $manager->create(1200, 800)->fill('cccccc');
$img->save($imagePath);

echo "Created test image at $imagePath\n";

// 4. Apply Watermark
PhotoWatermarkService::apply($photo);

echo "Watermark applied. Check the file at: " . $imagePath . "\n";
