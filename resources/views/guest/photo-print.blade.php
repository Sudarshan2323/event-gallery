<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Print Photo</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700|inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <style>
            @page {
                margin: 4mm;
                size: auto portrait;
            }

            :root {
                color-scheme: light;
            }

            * {
                box-sizing: border-box;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            body {
                margin: 0;
                background: #ede6da;
                color: #171717;
                font-family: 'Inter', sans-serif;
            }

            .page {
                width: min(100%, 860px);
                margin: 0 auto;
                padding: 18px;
            }

            .toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 14px;
            }

            .toolbar-copy {
                margin: 0;
                color: #5f564a;
                font-size: 14px;
            }

            .toolbar-actions {
                display: flex;
                gap: 10px;
            }

            .toolbar-actions button,
            .toolbar-actions a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 40px;
                padding: 0 16px;
                border-radius: 999px;
                border: 1px solid #d7c6a8;
                background: #fffaf1;
                color: #171717;
                text-decoration: none;
                font: inherit;
                font-weight: 600;
                cursor: pointer;
            }

            .print-card {
                background: #fbf8f1;
                border: 1px solid #dcc7a6;
                border-radius: 28px;
                box-shadow: 0 22px 56px rgba(0, 0, 0, 0.12);
                padding: 14px;
            }

            .print-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 12px;
            }

            .header-copy {
                min-width: 0;
            }

            .eyebrow {
                margin: 0;
                color: #9b7040;
                font-size: 10px;
                font-weight: 800;
                letter-spacing: 0.18em;
                text-transform: uppercase;
            }

            .title {
                margin: 5px 0 0;
                font-family: 'Playfair Display', serif;
                font-size: clamp(24px, 4vw, 42px);
                line-height: 1;
                color: #1c1a17;
            }

            .meta {
                margin: 4px 0 0;
                color: #5f564a;
                font-size: 13px;
                line-height: 1.35;
            }

            .header-thumb {
                width: 58px;
                height: 58px;
                padding: 5px;
                border-radius: 18px;
                border: 1px solid #dcc7a6;
                background: #fff;
                flex-shrink: 0;
            }

            .header-thumb img {
                display: block;
                width: 100%;
                height: 100%;
                border-radius: 12px;
                object-fit: cover;
            }

            .photo-frame {
                position: relative;
                overflow: hidden;
                border-radius: 24px;
                border: 1px solid #c7b092;
                background: #e6dbc8;
                aspect-ratio: 4 / 5;
            }

            .photo-frame::after {
                content: '';
                position: absolute;
                inset: auto 0 0;
                height: 32%;
                background: linear-gradient(to top, rgba(20, 16, 10, 0.24), rgba(20, 16, 10, 0));
                pointer-events: none;
            }

            .photo-frame img.main-photo {
                display: block;
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .overlay-row {
                position: absolute;
                left: 14px;
                right: 14px;
                bottom: 14px;
                z-index: 2;
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 12px;
            }

            .brand-panel,
            .qr-panel {
                border-radius: 18px;
                border: 1px solid rgba(42, 33, 18, 0.12);
                box-shadow: 0 10px 26px rgba(0, 0, 0, 0.12);
            }

            .brand-panel {
                min-width: 0;
                max-width: 46%;
                padding: 12px 14px;
                background: rgba(255, 248, 237, 0.94);
            }

            .brand-label {
                margin: 0;
                color: #9b7040;
                font-size: 9px;
                font-weight: 800;
                letter-spacing: 0.16em;
                text-transform: uppercase;
            }

            .brand-name {
                margin: 4px 0 0;
                font-family: 'Playfair Display', serif;
                font-size: 16px;
                line-height: 1.05;
                color: #201c16;
            }

            .brand-meta {
                margin: 5px 0 0;
                color: #5f564a;
                font-size: 10px;
                line-height: 1.35;
            }

            .qr-panel {
                display: flex;
                align-items: center;
                gap: 10px;
                min-width: 0;
                max-width: 42%;
                padding: 10px 12px;
                background: rgba(255, 255, 255, 0.96);
            }

            .qr-code {
                width: 56px;
                height: 56px;
                padding: 4px;
                border-radius: 12px;
                border: 1px solid #ded4c5;
                background: #fff;
                flex-shrink: 0;
            }

            .qr-code img {
                display: block;
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .qr-label {
                margin: 0;
                color: #9b7040;
                font-size: 8px;
                font-weight: 800;
                letter-spacing: 0.14em;
                text-transform: uppercase;
            }

            .qr-title {
                margin: 3px 0 0;
                color: #1d1b18;
                font-size: 12px;
                font-weight: 700;
                line-height: 1.15;
            }

            .qr-copy {
                margin: 4px 0 0;
                color: #5f564a;
                font-size: 9px;
                line-height: 1.3;
            }

            .screen-note {
                margin: 10px 0 0;
                color: #5f564a;
                font-size: 12px;
                text-align: center;
            }

            @media (max-width: 700px) {
                .page {
                    padding: 12px;
                }

                .toolbar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .print-header {
                    gap: 10px;
                }

                .overlay-row {
                    flex-direction: column;
                    align-items: stretch;
                }

                .brand-panel,
                .qr-panel {
                    max-width: none;
                }
            }

            @media print {
                body {
                    background: #fff;
                }

                .page {
                    width: 182mm;
                    padding: 0;
                }

                .toolbar,
                .screen-note {
                    display: none !important;
                }

                .print-card {
                    border: 0;
                    border-radius: 0;
                    box-shadow: none;
                    padding: 0;
                    background: transparent;
                    break-inside: avoid-page;
                    page-break-inside: avoid;
                }

                .print-header {
                    margin-bottom: 8px;
                    gap: 10px;
                    break-after: avoid-page;
                    page-break-after: avoid;
                }

                .eyebrow {
                    font-size: 8px;
                    letter-spacing: 0.14em;
                }

                .title {
                    margin-top: 3px;
                    font-size: 18px;
                }

                .meta {
                    margin-top: 3px;
                    font-size: 9px;
                    line-height: 1.25;
                }

                .header-thumb {
                    width: 44px;
                    height: 44px;
                    padding: 4px;
                    border-radius: 12px;
                }

                .header-thumb img {
                    border-radius: 8px;
                }

                .photo-frame {
                    height: 220mm;
                    aspect-ratio: auto;
                    break-inside: avoid-page;
                    page-break-inside: avoid;
                }

                .overlay-row {
                    left: 10px;
                    right: 10px;
                    bottom: 10px;
                    gap: 10px;
                }

                .brand-panel {
                    max-width: 44%;
                    padding: 10px 12px;
                    border-radius: 14px;
                }

                .brand-label {
                    font-size: 7px;
                }

                .brand-name {
                    margin-top: 3px;
                    font-size: 12px;
                }

                .brand-meta {
                    margin-top: 4px;
                    font-size: 8px;
                }

                .qr-panel {
                    max-width: 40%;
                    padding: 8px 10px;
                    gap: 8px;
                    border-radius: 14px;
                }

                .qr-code {
                    width: 48px;
                    height: 48px;
                    border-radius: 10px;
                }

                .qr-label {
                    font-size: 7px;
                }

                .qr-title {
                    font-size: 10px;
                }

                .qr-copy {
                    font-size: 7px;
                }
            }
        </style>
    </head>
    <body>
        <div class="page">
            <div class="toolbar">
                <p class="toolbar-copy">Single-page print layout with the same look as your sample.</p>
                <div class="toolbar-actions">
                    <button type="button" onclick="window.print()">Print</button>
                    <a href="{{ route('guest.photo.show', $photo->id) }}">Back To Photo</a>
                </div>
            </div>

            <div class="print-card">
                <div class="print-header">
                    <div class="header-copy">
                        <p class="eyebrow">Royal Events Print</p>
                        <h1 class="title">{{ $photo->event->couple_name ?: $photo->event->event_name }}</h1>
                        <p class="meta">
                            {{ $photo->event->event_name }}
                            @if($photo->event->location)
                                &bull; {{ $photo->event->location }}
                            @endif
                            &bull; Photo #{{ $photo->id }}
                        </p>
                    </div>

                    <div class="header-thumb">
                        <img src="{{ url(Storage::url($photo->image_path)) }}" alt="Photo thumbnail">
                    </div>
                </div>

                <div class="photo-frame">
                    <img class="main-photo" src="{{ url(Storage::url($photo->image_path)) }}" alt="Printable photo">

                    <div class="overlay-row">
                        <div class="brand-panel">
                            <p class="brand-label">Captured By</p>
                            <p class="brand-name">ROYAL EVENTS</p>
                            <p class="brand-meta">
                                {{ $photo->event->event_name }}
                                @if($photo->event->event_date)
                                    &bull; {{ \Carbon\Carbon::parse($photo->event->event_date)->format('F j, Y') }}
                                @endif
                            </p>
                        </div>

                        @if($photo->qr_code_path)
                            <div class="qr-panel">
                                <div class="qr-code">
                                    <img src="{{ url(Storage::url($photo->qr_code_path)) }}" alt="Photo QR code">
                                </div>
                                <div>
                                    <p class="qr-label">Scan To Open</p>
                                    <p class="qr-title">Digital Photo</p>
                                    <p class="qr-copy">Use this QR to view or download the same photo on your phone.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <p class="screen-note">If your browser still adds date or URL around the page, turn off Headers and footers in the print dialog.</p>
        </div>

        <script>
            const images = Array.from(document.images);
            const waitForImages = Promise.all(images.map((image) => {
                if (image.complete) {
                    return Promise.resolve();
                }

                return new Promise((resolve) => {
                    image.addEventListener('load', resolve, { once: true });
                    image.addEventListener('error', resolve, { once: true });
                });
            }));

            waitForImages.then(() => {
                window.setTimeout(() => window.print(), 250);
            });
        </script>
    </body>
</html>
