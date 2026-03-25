<!-- Tailwind CSS Play CDN -->
<script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Figtree', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
                    serif: ['Playfair Display', 'serif'],
                },
            },
        },
    }
</script>

<!-- Axios CDN -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    window.axios = axios;
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
</script>

<!-- Pusher & Echo CDNs -->
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: '{{ env('REVERB_APP_KEY') }}',
        wsHost: '{{ env('REVERB_HOST') }}',
        wsPort: {{ env('REVERB_PORT', 80) }},
        wssPort: {{ env('REVERB_PORT', 443) }},
        forceTLS: {{ env('REVERB_SCHEME', 'https') === 'https' ? 'true' : 'false' }},
        enabledTransports: ['ws', 'wss'],
    });
</script>

<!-- AlpineJS CDN -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    [x-cloak] { display: none !important; }
</style>
