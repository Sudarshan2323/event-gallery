<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Photo;
use App\Support\QrCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    public function index()
    {
        $events = Event::latest()->paginate(10, ['*'], 'events_page');
        $photos = Photo::with('event')->latest()->paginate(24, ['*'], 'photos_page');

        return view('admin.qrcodes.index', compact('events', 'photos'));
    }

    public function regenerateEvent(Request $request, Event $event)
    {
        $eventQrPath = 'events/' . $event->slug . '/event-qr.svg';
        $eventQrSvg = QrCodeGenerator::svg(url('/event/' . $event->slug), 320);
        Storage::disk('public')->put($eventQrPath, $eventQrSvg);

        $event->update(['qr_code_path' => $eventQrPath]);

        return back()->with('success', 'Event QR code regenerated.');
    }

    public function regeneratePhoto(Request $request, Photo $photo)
    {
        $photo->loadMissing('event');

        $qrPath = 'events/' . $photo->event->slug . '/qr/' . $photo->id . '.svg';
        $qrSvg = QrCodeGenerator::svg(url('/photo/' . $photo->id), 300);
        Storage::disk('public')->put($qrPath, $qrSvg);

        $photo->update(['qr_code_path' => $qrPath]);

        return back()->with('success', 'Photo QR code regenerated.');
    }

    public function download(Request $request)
    {
        $path = $request->query('path');
        
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'QR code not found.');
        }

        return Storage::disk('public')->download($path);
    }
}

