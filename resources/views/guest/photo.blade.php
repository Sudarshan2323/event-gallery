@extends('layouts.public')

@section('title', 'Photo Details')

@section('content')
<div class="min-h-screen bg-gray-950 text-white flex flex-col">
    <!-- Navbar / Back Link -->
    <div class="bg-gray-900 border-b border-gray-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <a href="{{ route('guest.event.show', $photo->event->slug) }}" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-white transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to {{ $photo->event->event_name }}
            </a>
        </div>
    </div>

    <div class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Main Photo Viewer -->
            <div class="lg:col-span-8 space-y-6">
                <div class="relative bg-black rounded-3xl overflow-hidden shadow-2xl border border-gray-800 group h-[70vh] flex items-center justify-center">
                    <img src="{{ Storage::url($photo->image_path) }}" alt="Photo ID {{ $photo->id }}" class="max-h-full w-auto object-contain transition-transform duration-500" id="main-photo">
                    
                    <!-- Top Actions Overlay -->
                    <div class="absolute top-6 right-6 flex gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ Storage::url($photo->image_path) }}" target="_blank" class="p-3 bg-white/10 hover:bg-white/20 rounded-full backdrop-blur-md border border-white/10 transition-all" title="View Fullscreen">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Simple Thumbnails / Context -->
                <div class="flex items-center justify-between text-gray-400 px-2">
                    <p class="text-xs uppercase tracking-widest font-bold">Photo No. #{{ $photo->id }}</p>
                    <p class="text-xs uppercase tracking-widest font-bold">Uploaded {{ $photo->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Enhanced Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Info Card -->
                <div class="bg-gray-900 rounded-3xl p-8 border border-gray-800 shadow-xl">
                    <h1 class="text-2xl font-serif text-white mb-2">{{ $photo->event->couple_name ?: $photo->event->event_name }}</h1>
                    <div class="flex items-center text-indigo-400 text-sm font-medium mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $photo->event->location ?: 'Event Location' }}
                    </div>

                    <div class="space-y-4">
                        <a href="{{ route('guest.photo.download', $photo->id) }}" class="flex items-center justify-center w-full h-14 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-indigo-500/40 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Original
                        </a>
                        
                        <button onclick="shareLink()" class="flex items-center justify-center w-full h-14 bg-gray-800 hover:bg-gray-700 text-white font-bold rounded-2xl transition-all border border-gray-700">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                            Share This Moment
                        </button>

                        <button onclick="printPhoto()" class="flex items-center justify-center w-full h-14 bg-white/10 hover:bg-white/20 text-white font-bold rounded-2xl transition-all border border-white/10">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 18h12v2H6v-2zm0-6h12v6H6v-6z"></path></svg>
                            Print This Photo
                        </button>
                    </div>

                    <div id="toast" class="mt-4 text-center text-green-400 text-sm font-medium hidden">
                        Link copied successfully!
                    </div>
                </div>

                <!-- QR Card -->
                @if($photo->qr_code_path)
                <div class="bg-gray-900 rounded-3xl p-8 border border-gray-800 shadow-xl text-center">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-6">Quick Link QR</p>
                    <div class="p-4 bg-white rounded-2xl inline-block mb-6 shadow-inner ring-1 ring-white/10">
                        <img src="{{ Storage::url($photo->qr_code_path) }}" alt="QR Code" class="w-40 h-40">
                    </div>
                    <a href="{{ route('guest.qrcode.download') }}?path={{ urlencode($photo->qr_code_path) }}" class="flex items-center justify-center w-full h-12 bg-gray-800 hover:bg-gray-700 text-gray-200 text-sm font-bold rounded-xl transition-all border border-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download QR Code
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function shareLink() {
    const url = window.location.href;
    if (navigator.share) {
        navigator.share({ title: 'Photo from {{ $photo->event->event_name }}', url });
    } else {
        navigator.clipboard.writeText(url).then(() => {
            const toast = document.getElementById('toast');
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        });
    }
}

function printPhoto() {
    const printUrl = @js(route('guest.photo.print', $photo->id));
    const w = window.open(printUrl, '_blank', 'noopener');
    if (!w) {
        alert('Popup blocked. Allow popups to print.');
    }
}
</script>
@endsection
