<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Support\QrCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount('photos')->latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'couple_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'organizer_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('organizer_logo')) {
            $logoPath = $request->file('organizer_logo')->store('event-logos', 'public');
        }

        $baseSlug = Str::slug($request->event_name);
        if ($baseSlug === '') {
            $baseSlug = 'event';
        }
        $slug = $baseSlug;
        $suffix = 2;
        while (Event::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $event = Event::create([
            'event_name' => $request->event_name,
            'couple_name' => $request->couple_name,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'description' => $request->description,
            'organizer_logo' => $logoPath,
            'slug' => $slug,
        ]);

        // Generate event QR code (links to guest event gallery)
        $eventQrPath = 'events/' . $event->slug . '/event-qr.svg';
        $eventQrSvg = QrCodeGenerator::svg(url('/event/' . $event->slug), 320);
        Storage::disk('public')->put($eventQrPath, $eventQrSvg);
        $event->update(['qr_code_path' => $eventQrPath]);

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully!');
    }

    public function show(Event $event)
    {
        $photos = $event->photos()->latest()->paginate(24);

        return view('admin.events.show', compact('event', 'photos'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'couple_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'organizer_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $logoPath = $event->organizer_logo;
        if ($request->hasFile('organizer_logo')) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('organizer_logo')->store('event-logos', 'public');
        }

        $event->update([
            'event_name' => $request->event_name,
            'couple_name' => $request->couple_name,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'description' => $request->description,
            'organizer_logo' => $logoPath,
        ]);

        return redirect()->route('admin.events.show', $event)->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        if ($event->organizer_logo) {
            Storage::disk('public')->delete($event->organizer_logo);
        }

        // Remove all event-related files (photos, photo QR codes, event QR code).
        Storage::disk('public')->deleteDirectory('events/' . $event->slug);

        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }
}
