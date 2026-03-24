<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Photo;
use App\Events\PhotoUploaded;
use App\Support\QrCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        $photos = Photo::with('event')->latest()->paginate(24);
        return view('admin.photos.index', compact('photos'));
    }

    public function create()
    {
        $events = Event::orderBy('event_date', 'desc')->get();
        return view('admin.photos.create', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'photos' => 'required|array',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $event = Event::findOrFail($request->event_id);

        foreach ($request->file('photos') as $file) {
            $path = $file->store('events/' . $event->slug, 'public');
            
            $photo = Photo::create([
                'event_id' => $event->id,
                'image_path' => $path,
                'downloads' => 0,
            ]);

            // Generate specific QR Code for each uploaded photo
            $qrPath = 'events/' . $event->slug . '/qr/' . $photo->id . '.svg';
            $qrCodeData = QrCodeGenerator::svg(url('/photo/' . $photo->id), 300);
            Storage::disk('public')->put($qrPath, $qrCodeData);

            $photo->update(['qr_code_path' => $qrPath]);

            // Apply watermarking (Logo & QR)
            \App\Services\PhotoWatermarkService::apply($photo);

            // Fire WebSocket event for real-time guest gallery.
            // If Reverb isn't running locally, we don't want uploads to fail.
            try {
                broadcast(new PhotoUploaded($photo))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('Broadcast failed (PhotoUploaded). Is Reverb running?', [
                    'photo_id' => $photo->id,
                    'event_id' => $event->id,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('admin.photos.index')->with('success', 'Photos uploaded successfully!');
    }

    public function destroy(Photo $photo)
    {
        Storage::disk('public')->delete($photo->image_path);
        if ($photo->qr_code_path) {
            Storage::disk('public')->delete($photo->qr_code_path);
        }
        $photo->delete();

        return back()->with('success', 'Photo deleted successfully.');
    }

    public function download(Photo $photo)
    {
        if (! Storage::disk('public')->exists($photo->image_path)) {
            abort(404, 'Photo not found.');
        }

        return Storage::disk('public')->download($photo->image_path);
    }
}
