<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $event->event_name }} - Selfie Booth</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700|inter:300,400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
            .font-serif { font-family: 'Playfair Display', serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-black text-white overflow-hidden">
        <div class="fixed inset-0"
            x-data="{
                state: 'idle', // idle | live | preview | uploading | done
                facingMode: 'user',
                stream: null,
                capturedBlob: null,
                capturedUrl: '',
                uploaded: null,
                error: '',
                flash: false,
                toast: '',
                uploadUrl: @js(route('guest.event.booth.upload', $event->slug)),
                eventName: @js($event->event_name),
                async requestFullscreen() {
                    try {
                        if (document.fullscreenEnabled && !document.fullscreenElement) {
                            await document.documentElement.requestFullscreen();
                        }
                    } catch (e) {
                        // Ignore (fullscreen often blocked).
                    }
                },
                async startCamera() {
                    this.error = '';
                    this.uploaded = null;
                    this.state = 'idle';

                    if (!navigator.mediaDevices?.getUserMedia) {
                        this.error = 'Camera is not supported in this browser.';
                        return;
                    }

                    await this.requestFullscreen();

                    try {
                        const constraints = {
                            audio: false,
                            video: {
                                facingMode: { ideal: this.facingMode },
                                width: { ideal: 1920 },
                                height: { ideal: 1080 },
                            },
                        };

                        this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                        this.$refs.video.srcObject = this.stream;
                        await this.$refs.video.play();
                        this.state = 'live';
                    } catch (e) {
                        this.error = 'Camera permission denied or camera unavailable.';
                        this.stopCamera();
                    }
                },
                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach((t) => t.stop());
                    }
                    this.stream = null;
                },
                async switchCamera() {
                    if (this.state !== 'live') return;
                    this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
                    this.stopCamera();
                    await this.startCamera();
                },
                async capture() {
                    if (this.state !== 'live') return;

                    const video = this.$refs.video;
                    const vw = video.videoWidth || 1280;
                    const vh = video.videoHeight || 720;

                    // Downscale to keep upload size reasonable.
                    const maxW = 1400;
                    const scale = Math.min(1, maxW / vw);
                    const w = Math.round(vw * scale);
                    const h = Math.round(vh * scale);

                    const canvas = this.$refs.canvas;
                    canvas.width = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');

                    ctx.save();
                    // Mirror "user" camera to match the on-screen preview.
                    if (this.facingMode === 'user') {
                        ctx.translate(w, 0);
                        ctx.scale(-1, 1);
                    }
                    ctx.drawImage(video, 0, 0, w, h);
                    ctx.restore();

                    this.flash = true;
                    setTimeout(() => this.flash = false, 120);

                    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.92));
                    if (!blob) {
                        this.error = 'Could not capture image.';
                        return;
                    }

                    if (this.capturedUrl) URL.revokeObjectURL(this.capturedUrl);
                    this.capturedBlob = blob;
                    this.capturedUrl = URL.createObjectURL(blob);
                    this.state = 'preview';
                },
                retake() {
                    this.uploaded = null;
                    this.error = '';
                    this.capturedBlob = null;
                    if (this.capturedUrl) URL.revokeObjectURL(this.capturedUrl);
                    this.capturedUrl = '';
                    this.state = this.stream ? 'live' : 'idle';
                },
                downloadLocal() {
                    if (!this.capturedUrl) return;
                    const a = document.createElement('a');
                    a.href = this.capturedUrl;
                    a.download = `selfie-${Date.now()}.jpg`;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    this.toast = 'Downloaded. You can save it to your gallery.';
                    setTimeout(() => this.toast = '', 2600);
                },
                async shareLocal() {
                    if (!this.capturedBlob) return;
                    const file = new File([this.capturedBlob], `selfie-${Date.now()}.jpg`, { type: this.capturedBlob.type || 'image/jpeg' });
                    try {
                        if (navigator.share && navigator.canShare?.({ files: [file] })) {
                            await navigator.share({ title: this.eventName, files: [file] });
                        } else {
                            this.downloadLocal();
                        }
                    } catch (e) {
                        // User cancelled.
                    }
                },
                printLocal() {
                    const src = this.capturedUrl;
                    if (!src) return;

                    const w = window.open('', '_blank');
                    if (!w) {
                        alert('Popup blocked. Allow popups to print.');
                        return;
                    }

                    w.document.write(`<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Print Selfie</title>
    <style>
      html, body { height: 100%; margin: 0; }
      body { display: flex; align-items: center; justify-content: center; background: #000; }
      img { max-width: 100%; max-height: 100vh; object-fit: contain; }
    </style>
  </head>
  <body>
    <img src="${src}" onload="window.print(); setTimeout(() => window.close(), 600);" />
  </body>
</html>`);
                    w.document.close();
                },
                async upload() {
                    if (!this.capturedBlob || this.state !== 'preview') return;

                    this.state = 'uploading';
                    this.error = '';

                    try {
                        const fd = new FormData();
                        fd.append('photo', new File([this.capturedBlob], `selfie-${Date.now()}.jpg`, { type: this.capturedBlob.type || 'image/jpeg' }));

                        const res = await fetch(this.uploadUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '',
                                'Accept': 'application/json',
                            },
                            body: fd,
                        });

                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            const message = data?.message || 'Upload failed.';
                            this.error = message;
                            this.state = 'preview';
                            return;
                        }

                        this.uploaded = data.photo;
                        this.state = 'done';
                        this.toast = 'Uploaded to the event gallery!';
                        setTimeout(() => this.toast = '', 2600);
                    } catch (e) {
                        this.error = 'Upload failed. Please try again.';
                        this.state = 'preview';
                    }
                },
                async shareUploaded() {
                    if (!this.uploaded?.public_url) return;
                    try {
                        if (navigator.share) {
                            await navigator.share({ title: this.eventName, url: this.uploaded.public_url });
                        } else {
                            await navigator.clipboard.writeText(this.uploaded.public_url);
                            this.toast = 'Link copied!';
                            setTimeout(() => this.toast = '', 2600);
                        }
                    } catch (e) {
                        // User cancelled or clipboard blocked.
                    }
                },
                cleanup() {
                    this.stopCamera();
                    if (this.capturedUrl) URL.revokeObjectURL(this.capturedUrl);
                },
            }"
            @keydown.escape.window="cleanup(); if (document.fullscreenElement) document.exitFullscreen().catch(()=>{}); window.location.href = @js(route('guest.event.show', $event->slug));"
            x-init="window.addEventListener('beforeunload', () => cleanup())"
        >
            <!-- Video Layer -->
            <div class="absolute inset-0" x-show="state === 'live'" x-cloak>
                <video
                    x-ref="video"
                    playsinline
                    muted
                    class="h-full w-full object-cover transform"
                    :class="facingMode === 'user' ? '-scale-x-100' : ''"
                ></video>

                <!-- Framing Overlay -->
                <div class="pointer-events-none absolute inset-0">
                    <div class="absolute inset-0 bg-gradient-to-b from-black/35 via-transparent to-black/55"></div>
                    <div class="absolute inset-6 sm:inset-10 rounded-3xl border border-white/15"></div>
                </div>

                <!-- Flash -->
                <div class="absolute inset-0 bg-white" x-show="flash" x-transition.opacity.duration.150ms style="display:none;"></div>
            </div>

            <!-- Preview Layer -->
            <div class="absolute inset-0 bg-black" x-show="state === 'preview' || state === 'uploading' || state === 'done'" x-cloak>
                <img :src="capturedUrl" alt="Captured selfie" class="h-full w-full object-contain bg-black">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/35 via-transparent to-black/70"></div>
            </div>

            <!-- Top Bar -->
            <div class="absolute top-0 left-0 right-0 z-20 p-4 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs uppercase tracking-widest text-white/70 font-semibold">Selfie Booth</p>
                        <h1 class="font-serif text-xl sm:text-2xl font-bold truncate">{{ $event->event_name }}</h1>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('guest.event.show', $event->slug) }}" class="inline-flex items-center px-3 py-2 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 text-sm font-semibold">
                            Back to Gallery
                        </a>
                    </div>
                </div>

                <div x-show="error" x-transition class="mt-3 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-100" style="display:none;">
                    <span x-text="error"></span>
                </div>
            </div>

            <!-- Bottom Controls -->
            <div class="absolute bottom-0 left-0 right-0 z-20 p-4 sm:p-6">
                <div class="mx-auto max-w-4xl">
                    <div class="rounded-3xl border border-white/10 bg-black/40 backdrop-blur-xl shadow-2xl p-4 sm:p-6">
                        <!-- Idle -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3" x-show="state === 'idle'" x-cloak>
                            <button type="button"
                                class="flex-1 h-12 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-lg"
                                @click="startCamera()"
                            >
                                Start Camera
                            </button>
                            <p class="text-xs text-white/70 sm:max-w-xs">
                                Allow camera permission. Tip: For best results, use mobile Safari/Chrome on <span class="font-semibold">localhost</span> or <span class="font-semibold">HTTPS</span>.
                            </p>
                        </div>

                        <!-- Live -->
                        <div class="flex items-center justify-between gap-3" x-show="state === 'live'" x-cloak>
                            <button type="button"
                                class="h-12 px-4 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-sm font-semibold"
                                @click="switchCamera()"
                                title="Switch camera"
                            >
                                Switch
                            </button>

                            <button type="button"
                                class="h-16 w-16 rounded-full bg-white shadow-xl ring-4 ring-white/30 hover:scale-[1.03] transition-transform"
                                @click="capture()"
                                title="Capture"
                            ></button>

                            <button type="button"
                                class="h-12 px-4 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-sm font-semibold"
                                @click="stopCamera(); state = 'idle'"
                                title="Stop camera"
                            >
                                Stop
                            </button>
                        </div>

                        <!-- Preview -->
                        <div class="flex flex-col gap-3" x-show="state === 'preview'" x-cloak>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" class="h-12 px-4 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-sm font-semibold" @click="retake()">
                                    Retake
                                </button>
                                <button type="button" class="h-12 px-4 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-sm font-semibold" @click="downloadLocal()">
                                    Download
                                </button>
                                <button type="button" class="h-12 px-4 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-sm font-semibold" @click="shareLocal()">
                                    Share
                                </button>
                                <button type="button" class="h-12 px-4 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-sm font-semibold" @click="printLocal()">
                                    Print
                                </button>
                            </div>

                            <button type="button"
                                class="h-12 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-lg"
                                @click="upload()"
                            >
                                Upload to Event Gallery
                            </button>
                        </div>

                        <!-- Uploading -->
                        <div class="flex items-center justify-between gap-3" x-show="state === 'uploading'" x-cloak>
                            <div class="flex items-center gap-3 text-white/80">
                                <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <p class="text-sm font-semibold">Uploading...</p>
                            </div>
                            <button type="button" class="h-12 px-4 rounded-2xl bg-white/10 border border-white/10 text-sm font-semibold opacity-60 cursor-not-allowed" disabled>
                                Please wait
                            </button>
                        </div>

                        <!-- Done -->
                        <div class="flex flex-col gap-3" x-show="state === 'done'" x-cloak>
                            <div class="flex flex-wrap items-center gap-2">
                                <a :href="uploaded?.public_url" class="h-12 px-4 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-lg inline-flex items-center justify-center">
                                    Open Photo Page
                                </a>
                                <button type="button" class="h-12 px-4 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-sm font-semibold" @click="shareUploaded()">
                                    Share Link
                                </button>
                                <button type="button" class="h-12 px-4 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-sm font-semibold" @click="retake(); startCamera()">
                                    Take Another
                                </button>
                            </div>

                            <template x-if="uploaded?.qr_url">
                                <div class="mt-2 flex items-center gap-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="bg-white rounded-xl p-2">
                                        <img :src="uploaded.qr_url" alt="QR" class="h-20 w-20">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold">Scan to open your photo</p>
                                        <p class="mt-0.5 text-xs text-white/70 break-words" x-text="uploaded.public_url"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden canvas used for capture -->
            <canvas x-ref="canvas" class="hidden"></canvas>

            <!-- Toast -->
            <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50" x-show="toast" x-transition style="display:none;">
                <div class="rounded-full bg-black/70 backdrop-blur px-4 py-3 text-sm font-semibold border border-white/10 shadow-2xl">
                    <span x-text="toast"></span>
                </div>
            </div>
        </div>
    </body>
</html>

