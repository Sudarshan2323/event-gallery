@extends('layouts.admin')

@section('title', 'Event Details')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.events.index') }}" class="text-gray-500 hover:text-gray-700 bg-white p-2 rounded-md shadow-sm border border-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $event->event_name }}</h2>
                <p class="text-sm text-gray-500">{{ $event->couple_name }} &bull; {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('guest.event.show', $event->slug) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7v7m0-7L10 14m-4 0H3v-7"></path></svg>
                View Gallery
            </a>
            <a href="{{ route('guest.event.slideshow', $event->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-gray-900 rounded-lg text-sm font-semibold text-white hover:bg-gray-800">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14m-6 0H5a2 2 0 01-2-2V8a2 2 0 012-2h4l5-3v18l-5-3z"></path></svg>
                Slideshow
            </a>
            <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </a>
            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Delete this event? All photos and QR files will be removed from storage.')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Event Info</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Date</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</dd>
                </div>
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Location</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $event->location ?: 'N/A' }}</dd>
                </div>
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 sm:col-span-2">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Guest URL</dt>
                    <dd class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('guest.event.show', $event->slug) }}" target="_blank" class="text-sm font-medium text-indigo-700 break-all hover:underline">{{ route('guest.event.show', $event->slug) }}</a>
                        <button type="button" class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-lg bg-white border border-gray-200 hover:bg-gray-50"
                            onclick="navigator.clipboard.writeText('{{ route('guest.event.show', $event->slug) }}')">
                            Copy Link
                        </button>
                    </dd>
                </div>
                @if($event->description)
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 sm:col-span-2">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Description</dt>
                    <dd class="mt-1 text-sm text-gray-700 leading-relaxed">{{ $event->description }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Event QR Code</h3>

            @if($event->qr_code_path)
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex items-center justify-center">
                    <img src="{{ Storage::url($event->qr_code_path) }}" alt="Event QR Code" class="w-56 h-56">
                </div>
                <div class="mt-4 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.qrcodes.download') }}?path={{ urlencode($event->qr_code_path) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download QR
                    </a>
                    <span class="text-xs text-gray-500">Scans open the event gallery</span>
                </div>
            @else
                <div class="text-sm text-gray-500 bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-6 text-center">
                    No event QR code found for this event yet.
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Event Photos</h3>
                <p class="text-sm text-gray-500">Latest uploads appear first</p>
            </div>
            <a href="{{ route('admin.photos.create', ['event_id' => $event->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Upload Photos
            </a>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @forelse($photos as $photo)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group">
                        <div class="relative aspect-square bg-gray-100">
                            <img src="{{ Storage::url($photo->image_path) }}" alt="Photo {{ $photo->id }}" class="w-full h-full object-cover transition-transform group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <a href="{{ route('guest.photo.show', $photo->id) }}" target="_blank" class="p-2 bg-white text-gray-900 rounded-full hover:bg-gray-100" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('admin.photos.download', $photo->id) }}" class="p-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-500" title="Download">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                                @if($photo->qr_code_path)
                                    <a href="{{ route('admin.qrcodes.download') }}?path={{ urlencode($photo->qr_code_path) }}" class="p-2 bg-white text-gray-900 rounded-full hover:bg-gray-100" title="Download QR">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    </a>
                                @endif
                                <form action="{{ route('admin.photos.destroy', $photo) }}" method="POST" onsubmit="return confirm('Delete this photo?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-600 text-white rounded-full hover:bg-red-700" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="p-3 text-xs flex items-center justify-between text-gray-600">
                            <span>#{{ $photo->id }}</span>
                            <span>{{ $photo->downloads }} dl</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">
                        No photos yet. Use “Upload Photos” to add images for this event.
                    </div>
                @endforelse
            </div>
        </div>

        @if(method_exists($photos, 'links'))
        <div class="p-4 border-t border-gray-100">
            {{ $photos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
