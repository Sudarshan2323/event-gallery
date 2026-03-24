@extends('layouts.public')

@section('title', $event->event_name)

@section('content')
<!-- Hero Section -->
<div class="relative bg-white overflow-hidden shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        @if($event->organizer_logo)
            <img class="mx-auto h-24 w-auto rounded-xl object-contain mb-8 shadow-sm border border-gray-100 p-2" src="{{ Storage::url($event->organizer_logo) }}" alt="Organizer Logo">
        @else
            <div class="mx-auto h-20 w-20 rounded-full bg-indigo-50 text-indigo-500 font-serif flex items-center justify-center text-3xl font-bold mb-6 italic">
                {{ substr($event->couple_name ?? $event->event_name, 0, 1) }}
            </div>
        @endif
        
        <h1 class="text-4xl md:text-6xl font-serif text-gray-900 mb-4">{{ $event->couple_name ?: $event->event_name }}</h1>
        <p class="text-lg md:text-xl text-gray-500 uppercase tracking-widest font-medium">{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</p>
        
        @if($event->location)
            <p class="mt-2 text-md text-gray-400 font-serif italic">{{ $event->location }}</p>
        @endif

        @if($event->description)
            <p class="mt-6 max-w-2xl mx-auto text-base text-gray-600 leading-relaxed">{{ $event->description }}</p>
        @endif
    </div>
</div>

<!-- Gallery Section via AlpineJS -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" 
    x-data="{
        photos: {{ json_encode($event->photos) }},
        selectedPhoto: null,
        showQr: false,
        toast: '',
        init() {
            setTimeout(() => {
                if(window.Echo) {
                    window.Echo.channel('event.{{ $event->slug }}')
                        .listen('.PhotoUploaded', (e) => {
                            // Add animation flag
                            e.photo.isNew = true;
                            // Prepend new photo to array
                            this.photos.unshift(e.photo);
                            
                            // Remove glow after animation
                            setTimeout(() => {
                                let p = this.photos.find(i => i.id === e.photo.id);
                                if(p) p.isNew = false;
                            }, 3000);
                        });
                } else {
                    console.warn('Laravel Echo is not defined.');
                }
            }, 1000);
        },
        storageUrl(path) {
            return path ? ('/storage/' + path) : '';
        },
        photoUrl(photo) {
            return '/photo/' + photo.id;
        },
        downloadUrl(photo) {
            return '/photo/' + photo.id + '/download';
        },
        openPhoto(photo) {
            this.selectedPhoto = photo;
            this.showQr = false;
            document.body.classList.add('overflow-hidden');
        },
        openQr(photo) {
            this.selectedPhoto = photo;
            this.showQr = true;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.selectedPhoto = null;
            this.showQr = false;
            document.body.classList.remove('overflow-hidden');
        },
        async share(photo) {
            const url = window.location.origin + this.photoUrl(photo);
            try {
                if (navigator.share) {
                    await navigator.share({ title: '{{ $event->event_name }}', url });
                } else {
                    await navigator.clipboard.writeText(url);
                    this.toast = 'Link copied to clipboard!';
                    setTimeout(() => this.toast = '', 2500);
                }
            } catch (e) {
                // User cancelled or clipboard blocked
            }
        }
    }">
    
    <div class="flex justify-between items-stretch sm:items-center flex-col sm:flex-row mb-8 gap-4">
        <h2 class="text-2xl font-serif text-gray-900">Event Gallery</h2>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('guest.event.booth', $event->slug) }}" target="_blank" class="text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800 px-5 py-2.5 rounded-full shadow-sm flex items-center transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h2l2-3h10l2 3h2v13H3V7zm9 10a4 4 0 100-8 4 4 0 000 8z"></path></svg>
                Selfie Booth
            </a>
            <a href="{{ route('guest.event.download', $event->slug) }}" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-5 py-2.5 rounded-full shadow-sm flex items-center transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download All Photos
            </a>
            <div class="text-sm font-medium text-gray-500 bg-white px-4 py-2.5 rounded-full shadow-sm border border-gray-100 flex items-center">
                <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                Live Updates
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div x-show="photos.length === 0" class="text-center py-24 bg-white rounded-3xl shadow-sm border border-gray-100" style="display: none;">
        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <h3 class="mt-4 text-xl font-serif text-gray-900">No photos yet</h3>
        <p class="mt-2 text-gray-500">Photos taken at the event will appear here automatically.</p>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">
        <template x-for="photo in photos" :key="photo.id">
            <div class="group block relative rounded-2xl overflow-hidden aspect-[4/5] bg-gray-100 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl transform cursor-pointer"
               :class="photo.isNew ? 'ring-4 ring-indigo-400 ring-opacity-50 blur-0 scale-100 duration-500' : ''"
               @click="openPhoto(photo)"
               x-transition:enter="transition ease-out duration-500"
               x-transition:enter-start="opacity-0 translate-y-8 scale-95 blur-sm"
               x-transition:enter-end="opacity-100 translate-y-0 scale-100 blur-0">
                
                <img :src="'/storage/' + photo.image_path" loading="lazy" :alt="'Photo ' + photo.id" class="w-full h-full object-cover transition-transform duration-700 ease-in-out group-hover:scale-110">
                
                <!-- Overlay Gradients -->
                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <!-- Actions -->
                <div class="absolute inset-x-0 bottom-4 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <a :href="photoUrl(photo)" class="px-3 py-2 text-xs font-semibold text-white rounded-full border border-white/30 bg-black/30 backdrop-blur-sm hover:bg-black/40"
                       @click.stop>
                        View
                    </a>
                    <a :href="downloadUrl(photo)" class="p-2 rounded-full bg-indigo-600 text-white hover:bg-indigo-500 shadow-lg"
                       title="Download" @click.stop>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </a>
                    <button type="button" class="p-2 rounded-full bg-white/20 text-white hover:bg-white/30 backdrop-blur-sm"
                            title="Share" @click.stop="share(photo)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    </button>
                    <button type="button" class="p-2 rounded-full bg-white/20 text-white hover:bg-white/30 backdrop-blur-sm"
                            title="Show QR" @click.stop="openQr(photo)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h4v4H5V5zm10 0h4v4h-4V5zM5 15h4v4H5v-4zm7-7h1m2 0h1m-4 4h1m2 0h1m-7 2h6"></path></svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Toast -->
    <div x-show="toast" x-transition class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-sm font-semibold px-4 py-3 rounded-full shadow-xl" style="display: none;">
        <span x-text="toast"></span>
    </div>

    <!-- Lightbox / Modal -->
    <div x-show="selectedPhoto" x-cloak @keydown.escape.window="closeModal()" class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeModal()"></div>

        <div class="relative z-10 max-w-6xl mx-auto h-full px-4 sm:px-6 lg:px-8 flex items-center">
            <div class="w-full bg-white rounded-3xl overflow-hidden shadow-2xl border border-white/10">
                <div class="flex flex-col lg:flex-row">
                    <div class="flex-1 bg-gray-900 flex items-center justify-center p-4 lg:p-6">
                        <img :src="storageUrl(selectedPhoto?.image_path)" alt="Selected photo" class="max-h-[75vh] w-auto object-contain rounded-2xl ring-1 ring-white/10">
                    </div>
                    <div class="lg:w-96 p-6 lg:p-8">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Photo</p>
                                <p class="mt-1 text-lg font-bold text-gray-900">#<span x-text="selectedPhoto?.id"></span></p>
                            </div>
                            <button type="button" class="p-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200" @click="closeModal()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="mt-6 space-y-3">
                            <a :href="downloadUrl(selectedPhoto)" class="w-full h-12 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl flex justify-center items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download
                            </a>
                            <button type="button" class="w-full h-12 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl flex justify-center items-center" @click="share(selectedPhoto)">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                Share Link
                            </button>
                            <a :href="photoUrl(selectedPhoto)" class="w-full h-12 bg-white border border-gray-200 hover:bg-gray-50 text-gray-900 font-semibold rounded-xl flex justify-center items-center">
                                Open Details
                            </a>
                        </div>

                        <div class="mt-8">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">QR Code</p>
                                <button type="button" class="text-xs font-semibold text-indigo-700 hover:underline" @click="showQr = !showQr">Toggle</button>
                            </div>

                            <div class="mt-4" x-show="showQr" style="display: none;">
                                <template x-if="selectedPhoto?.qr_code_path">
                                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 text-center">
                                        <div class="bg-white rounded-xl p-3 inline-block border border-gray-200">
                                            <img :src="storageUrl(selectedPhoto?.qr_code_path)" alt="QR Code" class="w-44 h-44">
                                        </div>
                                        <a :href="storageUrl(selectedPhoto?.qr_code_path)" download class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                                            Download QR
                                        </a>
                                        <p class="mt-2 text-xs text-gray-500">Scan to open this photo</p>
                                    </div>
                                </template>
                                <template x-if="!selectedPhoto?.qr_code_path">
                                    <div class="text-sm text-gray-500 bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-6 text-center">
                                        QR not available for this photo yet.
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
