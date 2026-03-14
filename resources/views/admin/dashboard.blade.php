@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Stat Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Events -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md cursor-pointer">
            <div class="p-3 rounded-xl bg-blue-50 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Events</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalEvents) }}</p>
            </div>
        </div>
        
        <!-- Photos -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md cursor-pointer">
            <div class="p-3 rounded-xl bg-purple-50 text-purple-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Photos</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalPhotos) }}</p>
            </div>
        </div>

        <!-- Downloads -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md cursor-pointer">
            <div class="p-3 rounded-xl bg-green-50 text-green-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Downloads</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalDownloads) }}</p>
            </div>
        </div>

        <!-- QR Scans -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center transition-transform hover:-translate-y-1 hover:shadow-md cursor-pointer">
            <div class="p-3 rounded-xl bg-orange-50 text-orange-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H5v3a1 1 0 01-2 0V4zM21 4a1 1 0 00-1-1h-4a1 1 0 000 2h3v3a1 1 0 002 0V4zM3 20a1 1 0 001 1h4a1 1 0 000-2H5v-3a1 1 0 00-2 0v4zM21 20a1 1 0 01-1 1h-4a1 1 0 010-2h3v-3a1 1 0 012 0v4z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">QR Scans</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalScans) }}</p>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Events -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Recent Events</h3>
                <a href="{{ route('admin.events.index') }}" class="text-sm text-indigo-600 font-medium hover:text-indigo-800">View All</a>
            </div>
            <div class="p-0">
                @forelse($recentEvents as $event)
                <div class="p-4 border-b border-gray-50 hover:bg-gray-50 flex justify-between items-center transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                            {{ substr($event->couple_name ?? $event->event_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $event->event_name }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }} &bull; {{ $event->photos_count }} photos</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500 text-sm">No events found. Create one to get started.</div>
                @endforelse
            </div>
        </div>

        <!-- Latest Photos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Latest Uploads</h3>
                <a href="{{ route('admin.photos.index') }}" class="text-sm text-indigo-600 font-medium hover:text-indigo-800">View Gallery</a>
            </div>
            <div class="p-6 grid grid-cols-3 gap-3">
                @forelse($latestPhotos as $photo)
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden group relative">
                        <img src="{{ Storage::url($photo->image_path) }}" alt="Photo" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                    </div>
                @empty
                <div class="col-span-3 text-center text-gray-500 text-sm py-4">No photos uploaded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
