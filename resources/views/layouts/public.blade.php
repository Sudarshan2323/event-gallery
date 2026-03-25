<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700|inter:300,400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @include('partials.assets')
        
        <style>
            .font-serif {
                font-family: 'Playfair Display', serif;
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col">
        <!-- Main Content -->
        <main class="flex-1 w-full pb-12">
            @yield('content')
        </main>

        <!-- Dynamic Footer -->
        <footer class="bg-white border-t border-gray-100 py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
