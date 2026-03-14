<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Photo;
use App\Models\Analytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GuestController extends Controller
{
    public function showEvent($slug)
    {
        $event = Event::where('slug', $slug)->with(['photos' => function ($query) {
            $query->latest();
        }])->firstOrFail();
        return view('guest.event', compact('event'));
    }

    public function slideshowById(Event $event)
    {
        $event->load(['photos' => function ($query) {
            $query->latest();
        }]);

        return view('guest.slideshow', compact('event'));
    }

    public function showPhoto($id)
    {
        $photo = Photo::with('event')->findOrFail($id);
        
        // Track analytics for viewing/scanning
        Analytic::firstOrCreate(
            ['photo_id' => $photo->id, 'visitor_ip' => request()->ip()],
            ['qr_scans' => 0, 'downloads' => 0]
        )->increment('qr_scans');

        return view('guest.photo', compact('photo'));
    }

    public function downloadPhoto($id)
    {
        $photo = Photo::findOrFail($id);
        $photo->increment('downloads');
        
        Analytic::firstOrCreate(
            ['photo_id' => $photo->id, 'visitor_ip' => request()->ip()],
            ['qr_scans' => 0, 'downloads' => 0]
        )->increment('downloads');

        if (! Storage::disk('public')->exists($photo->image_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($photo->image_path);
    }

    public function downloadEvent($slug)
    {
        $event = Event::where('slug', $slug)->with('photos')->firstOrFail();
        
        if ($event->photos->isEmpty()) {
            return back()->with('error', 'No photos available to download.');
        }

        $zipFileName = $event->slug . '-photos.zip';
        $zipPath = storage_path('app/public/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!Storage::disk('public')->exists('temp')) {
            Storage::disk('public')->makeDirectory('temp');
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($event->photos as $photo) {
                $fullPath = storage_path('app/public/' . $photo->image_path);
                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, basename($fullPath));
                }
            }
            $zip->close();
        } else {
            return back()->with('error', 'Could not create ZIP file.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function slideshow($slug)
    {
        $event = Event::where('slug', $slug)->with(['photos' => function ($query) {
            $query->latest();
        }])->firstOrFail();
        return view('guest.slideshow', compact('event'));
    }

    public function downloadQr(Request $request)
    {
        $path = $request->query('path');
        
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'QR code not found.');
        }

        return Storage::disk('public')->download($path);
    }
}
