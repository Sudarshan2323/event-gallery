<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event->event_name }} – Selfie Booth</title>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #000;
            color: #fff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            height: 100dvh;
            overflow: hidden;
        }

        /* ── Layers ── */
        #booth-root { position: fixed; inset: 0; }

        #video-layer, #preview-layer {
            position: absolute; inset: 0;
            background: #000;
            display: none;
        }
        #video-layer.active, #preview-layer.active { display: block; }

        video {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        video.mirrored { transform: scaleX(-1); }

        #preview-img {
            width: 100%; height: 100%;
            object-fit: contain;
            background: #000;
        }

        canvas { display: none; }

        /* ── Overlays ── */
        .gradient-top {
            position: absolute; inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,.55) 0%, transparent 30%, transparent 65%, rgba(0,0,0,.7) 100%);
            pointer-events: none;
        }
        .frame-border {
            position: absolute; inset: 24px;
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 20px;
            pointer-events: none;
        }

        /* ── Flash ── */
        #flash {
            position: absolute; inset: 0;
            background: #fff;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.05s;
        }
        #flash.on { opacity: 1; }

        /* ── Top Bar ── */
        #top-bar {
            position: absolute; top: 0; left: 0; right: 0;
            z-index: 30;
            padding: 20px 24px 12px;
        }
        .top-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .event-label {
            font-size: 10px; font-weight: 600; letter-spacing: .12em;
            text-transform: uppercase; color: rgba(255,255,255,.65);
            margin-bottom: 2px;
        }
        .event-name {
            font-size: 20px; font-weight: 700;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            max-width: 230px;
        }
        .back-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 12px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.1);
            color: #fff; font-size: 13px; font-weight: 600;
            text-decoration: none; white-space: nowrap;
            backdrop-filter: blur(6px);
            transition: background .15s;
        }
        .back-btn:hover { background: rgba(255,255,255,.2); }

        #error-box {
            margin-top: 10px; padding: 10px 14px;
            background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.35);
            border-radius: 14px; font-size: 13px; color: #fca5a5;
            display: none;
        }
        #error-box.visible { display: block; }

        /* ── Bottom Controls ── */
        #bottom-bar {
            position: absolute; bottom: 0; left: 0; right: 0;
            z-index: 30; padding: 16px 20px calc(16px + env(safe-area-inset-bottom));
        }
        .controls-panel {
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 24px;
            padding: 16px 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Panels */
        .ctrl { display: none; }
        .ctrl.active { display: flex; }

        /* Idle */
        #ctrl-idle { flex-direction: column; gap: 10px; }
        .start-btn {
            width: 100%; height: 48px;
            background: #4f46e5; border: none; border-radius: 14px;
            color: #fff; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: background .15s;
        }
        .start-btn:hover { background: #4338ca; }
        .idle-hint { font-size: 12px; color: rgba(255,255,255,.6); text-align: center; line-height: 1.5; }

        /* Live */
        #ctrl-live { align-items: center; justify-content: space-between; gap: 12px; }
        .side-btn {
            height: 46px; padding: 0 18px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 14px; color: #fff;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: background .15s;
        }
        .side-btn:hover { background: rgba(255,255,255,.22); }
        .capture-btn {
            width: 68px; height: 68px;
            background: #fff; border: none;
            border-radius: 50%;
            box-shadow: 0 0 0 4px rgba(255,255,255,.3), 0 4px 20px rgba(0,0,0,.4);
            cursor: pointer; transition: transform .1s;
            flex-shrink: 0;
        }
        .capture-btn:active { transform: scale(.94); }

        /* Preview */
        #ctrl-preview { flex-direction: column; gap: 10px; }
        .preview-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .ghost-btn {
            height: 44px; padding: 0 16px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 12px; color: #fff;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: background .15s;
        }
        .ghost-btn:hover { background: rgba(255,255,255,.2); }
        .upload-btn {
            width: 100%; height: 48px;
            background: #4f46e5; border: none; border-radius: 14px;
            color: #fff; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: background .15s;
        }
        .upload-btn:hover { background: #4338ca; }

        /* Uploading */
        #ctrl-uploading { align-items: center; gap: 12px; }
        .spinner {
            width: 22px; height: 22px;
            border: 3px solid rgba(255,255,255,.2);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .uploading-text { font-size: 14px; font-weight: 600; color: rgba(255,255,255,.8); }

        /* Done */
        #ctrl-done { flex-direction: column; gap: 10px; }
        .done-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .primary-link {
            display: inline-flex; align-items: center; justify-content: center;
            height: 44px; padding: 0 18px;
            background: #4f46e5; border-radius: 12px;
            color: #fff; font-size: 13px; font-weight: 700;
            text-decoration: none; transition: background .15s;
        }
        .primary-link:hover { background: #4338ca; }
        .qr-card {
            display: flex; align-items: center; gap: 14px;
            padding: 14px; border-radius: 16px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            display: none;
        }
        .qr-card.visible { display: flex; }
        .qr-card img { width: 72px; height: 72px; background: #fff; border-radius: 10px; padding: 4px; }
        .qr-card-text h3 { font-size: 13px; font-weight: 700; }
        .qr-card-text p { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 3px; word-break: break-all; }

        /* Toast */
        #toast {
            position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%);
            z-index: 99;
            background: rgba(0,0,0,.75); backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 999px;
            padding: 10px 20px; font-size: 13px; font-weight: 600;
            white-space: nowrap; pointer-events: none;
            opacity: 0; transition: opacity .2s;
        }
        #toast.show { opacity: 1; }
    </style>
</head>
<body>

<div id="booth-root">

    {{-- Video Layer --}}
    <div id="video-layer">
        <video id="video" playsinline muted autoplay></video>
        <div class="gradient-top"></div>
        <div class="frame-border"></div>
        <div id="flash"></div>
    </div>

    {{-- Preview Layer --}}
    <div id="preview-layer">
        <img id="preview-img" src="" alt="Captured photo">
        <div class="gradient-top"></div>
    </div>

    {{-- Hidden Canvas --}}
    <canvas id="canvas"></canvas>

    {{-- Top Bar --}}
    <div id="top-bar">
        <div class="top-row">
            <div>
                <div class="event-label">Selfie Booth</div>
                <div class="event-name">{{ $event->event_name }}</div>
            </div>
            <a href="{{ route('guest.event.show', $event->slug) }}" class="back-btn">
                ← Back to Gallery
            </a>
        </div>
        <div id="error-box"></div>
    </div>

    {{-- Bottom Controls --}}
    <div id="bottom-bar">
        <div class="controls-panel">

            {{-- Idle --}}
            <div class="ctrl active" id="ctrl-idle">
                <button class="start-btn" id="btn-start">📷 Start Camera</button>
                <p class="idle-hint">Camera works on <strong>localhost</strong> or <strong>HTTPS</strong> only.</p>
            </div>

            {{-- Live --}}
            <div class="ctrl" id="ctrl-live">
                <button class="side-btn" id="btn-switch">🔄 Switch</button>
                <button class="capture-btn" id="btn-capture" title="Capture"></button>
                <button class="side-btn" id="btn-stop">✕ Stop</button>
            </div>

            {{-- Preview --}}
            <div class="ctrl" id="ctrl-preview">
                <div class="preview-actions">
                    <button class="ghost-btn" id="btn-retake">↩ Retake</button>
                    <button class="ghost-btn" id="btn-download">⬇ Download</button>
                    <button class="ghost-btn" id="btn-share-local">↑ Share</button>
                </div>
                <button class="upload-btn" id="btn-upload">⬆ Upload to Event Gallery</button>
            </div>

            {{-- Uploading --}}
            <div class="ctrl" id="ctrl-uploading">
                <div class="spinner"></div>
                <span class="uploading-text">Uploading your photo…</span>
            </div>

            {{-- Done --}}
            <div class="ctrl" id="ctrl-done">
                <div class="done-actions">
                    <a href="#" id="link-open-photo" class="primary-link" target="_blank">🖼 Open Photo Page</a>
                    <button class="ghost-btn" id="btn-share-uploaded">↑ Share Link</button>
                    <button class="ghost-btn" id="btn-another">📷 Take Another</button>
                </div>
                <div class="qr-card" id="qr-card">
                    <img id="qr-img" src="" alt="QR Code">
                    <div class="qr-card-text">
                        <h3>Scan to open your photo</h3>
                        <p id="qr-url"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="toast"></div>
</div>

<script>
(function () {
    // ── Config (from Laravel/Blade) ────────────────────────────────
    const UPLOAD_URL  = @js(route('guest.event.booth.upload', $event->slug));
    const GALLERY_URL = @js(route('guest.event.show', $event->slug));
    const EVENT_NAME  = @js($event->event_name);
    const CSRF_TOKEN  = document.querySelector('meta[name=csrf-token]').content;

    // ── State ──────────────────────────────────────────────────────
    let stream        = null;
    let facingMode    = 'user';
    let capturedBlob  = null;
    let capturedUrl   = '';
    let uploadedData  = null;

    // ── Elements ───────────────────────────────────────────────────
    const videoEl     = document.getElementById('video');
    const canvas      = document.getElementById('canvas');
    const previewImg  = document.getElementById('preview-img');
    const flash       = document.getElementById('flash');
    const errorBox    = document.getElementById('error-box');
    const toast       = document.getElementById('toast');
    const qrCard      = document.getElementById('qr-card');
    const qrImg       = document.getElementById('qr-img');
    const qrUrl       = document.getElementById('qr-url');
    const linkOpenPhoto = document.getElementById('link-open-photo');

    const layers = {
        video:   document.getElementById('video-layer'),
        preview: document.getElementById('preview-layer'),
    };

    const panels = {
        idle:      document.getElementById('ctrl-idle'),
        live:      document.getElementById('ctrl-live'),
        preview:   document.getElementById('ctrl-preview'),
        uploading: document.getElementById('ctrl-uploading'),
        done:      document.getElementById('ctrl-done'),
    };

    // ── Helpers ────────────────────────────────────────────────────
    function setState(state) {
        // Layers
        layers.video.classList.toggle('active',   state === 'live');
        layers.preview.classList.toggle('active', ['preview','uploading','done'].includes(state));

        // Panels
        Object.keys(panels).forEach(k => {
            panels[k].classList.toggle('active', k === state);
        });
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.add('visible');
    }
    function clearError() { errorBox.classList.remove('visible'); }

    let toastTimer;
    function showToast(msg, ms = 2600) {
        toast.textContent = msg;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), ms);
    }

    // ── Camera ─────────────────────────────────────────────────────
    async function startCamera() {
        clearError();
        if (!navigator.mediaDevices?.getUserMedia) {
            showError('Camera is not supported in this browser.');
            return;
        }
        try {
            const constraints = {
                audio: false,
                video: { facingMode: { ideal: facingMode }, width: { ideal: 1920 }, height: { ideal: 1080 } },
            };
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            videoEl.srcObject = stream;
            videoEl.classList.toggle('mirrored', facingMode === 'user');
            await videoEl.play();
            setState('live');
        } catch (e) {
            showError('Camera permission denied or camera not available. Please allow camera access and try again.');
        }
    }

    function stopCamera() {
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    }

    async function switchCamera() {
        stopCamera();
        facingMode = facingMode === 'user' ? 'environment' : 'user';
        await startCamera();
    }

    // ── Capture ────────────────────────────────────────────────────
    async function capture() {
        const vw = videoEl.videoWidth  || 1280;
        const vh = videoEl.videoHeight || 720;
        const maxW = 1400;
        const scale = Math.min(1, maxW / vw);
        canvas.width  = Math.round(vw * scale);
        canvas.height = Math.round(vh * scale);

        const ctx = canvas.getContext('2d');
        ctx.save();
        if (facingMode === 'user') {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);
        ctx.restore();

        // Flash
        flash.classList.add('on');
        setTimeout(() => flash.classList.remove('on'), 120);

        const blob = await new Promise(res => canvas.toBlob(res, 'image/jpeg', 0.92));
        if (!blob) { showError('Could not capture image. Please try again.'); return; }

        if (capturedUrl) URL.revokeObjectURL(capturedUrl);
        capturedBlob = blob;
        capturedUrl  = URL.createObjectURL(blob);
        previewImg.src = capturedUrl;
        setState('preview');
    }

    // ── Preview actions ────────────────────────────────────────────
    function retake() {
        capturedBlob = null;
        if (capturedUrl) URL.revokeObjectURL(capturedUrl);
        capturedUrl = '';
        previewImg.src = '';
        clearError();
        if (stream) { setState('live'); } else { setState('idle'); }
    }

    function downloadLocal() {
        if (!capturedUrl) return;
        const a = document.createElement('a');
        a.href = capturedUrl;
        a.download = `selfie-${Date.now()}.jpg`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        showToast('Downloaded to your device!');
    }

    async function shareLocal() {
        if (!capturedBlob) return;
        const file = new File([capturedBlob], `selfie-${Date.now()}.jpg`, { type: 'image/jpeg' });
        try {
            if (navigator.share && navigator.canShare?.({ files: [file] })) {
                await navigator.share({ title: EVENT_NAME, files: [file] });
            } else {
                downloadLocal();
            }
        } catch (e) { /* cancelled */ }
    }

    // ── Upload ─────────────────────────────────────────────────────
    async function upload() {
        if (!capturedBlob) return;
        clearError();
        setState('uploading');

        try {
            const fd = new FormData();
            fd.append('photo', new File([capturedBlob], `selfie-${Date.now()}.jpg`, { type: 'image/jpeg' }));

            const res = await fetch(UPLOAD_URL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: fd,
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                showError(data?.message || 'Upload failed. Please try again.');
                setState('preview');
                return;
            }

            uploadedData = data.photo;
            linkOpenPhoto.href = uploadedData.public_url || '#';

            if (uploadedData.qr_url) {
                qrImg.src = uploadedData.qr_url;
                qrUrl.textContent = uploadedData.public_url;
                qrCard.classList.add('visible');
            }

            setState('done');
            showToast('🎉 Uploaded to the event gallery!');
        } catch (e) {
            showError('Upload failed. Check your connection and try again.');
            setState('preview');
        }
    }

    async function shareUploaded() {
        const url = uploadedData?.public_url;
        if (!url) return;
        try {
            if (navigator.share) {
                await navigator.share({ title: EVENT_NAME, url });
            } else {
                await navigator.clipboard.writeText(url);
                showToast('Link copied to clipboard!');
            }
        } catch (e) { /* cancelled */ }
    }

    // ── Button wiring ──────────────────────────────────────────────
    document.getElementById('btn-start').onclick   = startCamera;
    document.getElementById('btn-switch').onclick  = switchCamera;
    document.getElementById('btn-capture').onclick = capture;
    document.getElementById('btn-stop').onclick    = () => { stopCamera(); setState('idle'); };
    document.getElementById('btn-retake').onclick  = retake;
    document.getElementById('btn-download').onclick = downloadLocal;
    document.getElementById('btn-share-local').onclick = shareLocal;
    document.getElementById('btn-upload').onclick  = upload;
    document.getElementById('btn-share-uploaded').onclick = shareUploaded;
    document.getElementById('btn-another').onclick = () => { retake(); startCamera(); };

    // Escape key → go back to gallery
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { stopCamera(); window.location.href = GALLERY_URL; }
    });

    // Cleanup on unload
    window.addEventListener('beforeunload', () => {
        stopCamera();
        if (capturedUrl) URL.revokeObjectURL(capturedUrl);
    });

})();
</script>

</body>
</html>
