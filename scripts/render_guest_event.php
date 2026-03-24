<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$slug = $argv[1] ?? 'amrut-and-saloni-2';

$event = \App\Models\Event::where('slug', $slug)->with('photos')->first();
if (! $event) {
    fwrite(STDERR, "Event not found for slug: {$slug}\n");
    exit(2);
}

try {
    view('guest.event', compact('event'))->render();
    echo "RENDER_OK\n";
} catch (\Throwable $e) {
    echo "RENDER_FAIL\n";
    echo $e . "\n";
    exit(1);
}

