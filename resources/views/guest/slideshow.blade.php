<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $event->event_name }} - Live Slideshow</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700|inter:300,400,500,600&display=swap" rel="stylesheet" />
        @include('partials.assets')
        <style>
            body { font-family: 'Inter', sans-serif; background-color: #000; overflow: hidden; }
            .font-serif { font-family: 'Playfair Display', serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="text-white h-screen w-screen"
        x-data="{
            photos: {{ json_encode($event->photos) }},
            currentIndex: 0,
            interval: null,
            init() {
                if (this.photos.length > 0) {
                    this.startSlideshow();
                }

                setTimeout(() => {
                    if(window.Echo) {
                        window.Echo.channel('event.{{ $event->slug }}')
                            .listen('.PhotoUploaded', (e) => {
                                // Add to beginning
                                this.photos.unshift(e.photo);
                                // Show the newly added photo immediately
                                this.currentIndex = 0;
                                this.resetSlideshow();
                            });
                    }
                }, 1000);
            },
            nextSlide() {
                if (this.photos.length === 0) return;
                this.currentIndex = (this.currentIndex + 1) % this.photos.length;
            },
            startSlideshow() {
                this.interval = setInterval(() => this.nextSlide(), 5000); // 5 seconds per slide
            },
            resetSlideshow() {
                clearInterval(this.interval);
                this.startSlideshow();
            }
        }">

        <div x-show="photos.length === 0" class="h-full w-full flex flex-col items-center justify-center">
            <h1 class="text-4xl font-serif text-gray-500">Waiting for photos...</h1>
            <p class="mt-4 text-gray-600">Scan event QR code to upload photos</p>
        </div>

        <template x-if="photos.length > 0">
            <div class="relative h-full w-full flex items-center justify-center">
                
                <!-- Background Blur -->
                <div class="absolute inset-0 z-0">
                    <img :src="'/storage/' + photos[currentIndex].image_path" class="w-full h-full object-cover blur-3xl opacity-30 transform scale-110">
                </div>
                
                <!-- Main Image -->
                <div class="relative z-10 w-full h-full flex items-center justify-center p-8">
                    <img :src="'/storage/' + photos[currentIndex].image_path" class="max-h-full max-w-full object-contain drop-shadow-2xl rounded-xl transition-all duration-1000 ease-in-out"
                         x-transition:enter="transition transform duration-1000"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100">
                </div>

                <!-- Overlay Info (QR & Event Details) -->
                <div class="absolute bottom-8 left-8 z-20 bg-black/50 backdrop-blur-md px-6 py-4 rounded-3xl border border-white/10 shadow-2xl flex items-center gap-6">
                    <template x-if="photos[currentIndex].qr_code_path">
                        <div class="bg-white p-2 rounded-xl">
                            <img :src="'/storage/' + photos[currentIndex].qr_code_path" alt="QR" class="w-24 h-24">
                        </div>
                    </template>
                    <div>
                        <h2 class="text-3xl font-serif font-bold text-white mb-1">{{ $event->event_name }}</h2>
                        <p class="text-indigo-300 font-medium">Scan QR to download this photo instantly</p>
                        <p class="text-sm text-gray-400 mt-2 flex items-center">
                            <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                            Live Sync Active
                        </p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="absolute top-0 left-0 w-full h-1 bg-white/10 z-20">
                    <div class="h-full bg-indigo-500 transition-all duration-[5000ms] ease-linear" 
                         :style="`width: 100%`"
                         x-init="$watch('currentIndex', () => { $el.style.width = '0%'; setTimeout(() => $el.style.transition='all 5000ms linear', 50); setTimeout(() => $el.style.width='100%', 100); })"></div>
                </div>
            </div>
        </template>
    </body>
</html>
