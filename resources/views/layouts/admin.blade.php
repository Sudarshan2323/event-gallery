<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900" x-data="{ sidebarOpen: true }">
        <div class="min-h-screen flex w-full">
            <!-- Sidebar -->
            <aside 
                class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transition-transform transform md:relative md:translate-x-0"
                :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
            >
                <div class="relative h-full flex flex-col">
                    <div class="h-16 flex items-center px-6 bg-gray-950 font-bold text-xl tracking-wider">
                        <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        {{ strtoupper(config('app.name', 'Event Gallery')) }}
                    </div>
                    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg @if(request()->routeIs('admin.dashboard')) bg-indigo-600 text-white @else text-gray-400 hover:bg-gray-800 hover:text-white @endif transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span class="font-medium">Dashboard</span>
                        </a>
                        <a href="{{ route('admin.events.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg @if(request()->routeIs('admin.events.*')) bg-indigo-600 text-white @else text-gray-400 hover:bg-gray-800 hover:text-white @endif transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="font-medium">Events</span>
                        </a>
                        <a href="{{ route('admin.photos.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg @if(request()->routeIs('admin.photos.*')) bg-indigo-600 text-white @else text-gray-400 hover:bg-gray-800 hover:text-white @endif transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="font-medium">Photos</span>
                        </a>
                        <a href="{{ route('admin.qrcodes.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg @if(request()->routeIs('admin.qrcodes.*')) bg-indigo-600 text-white @else text-gray-400 hover:bg-gray-800 hover:text-white @endif transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <span class="font-medium">QR Codes</span>
                        </a>
                        <a href="{{ route('admin.analytics.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg @if(request()->routeIs('admin.analytics.*')) bg-indigo-600 text-white @else text-gray-400 hover:bg-gray-800 hover:text-white @endif transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span class="font-medium">Analytics</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg @if(request()->routeIs('admin.settings.*')) bg-indigo-600 text-white @else text-gray-400 hover:bg-gray-800 hover:text-white @endif transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-12v2m0 12v2m8-8h-2m-12 0H4m15.364-5.364l-1.414 1.414M6.05 17.95l-1.414 1.414m0-13.314L6.05 6.464m11.9 11.9l1.414 1.414"></path></svg>
                            <span class="font-medium">Settings</span>
                        </a>
                    </nav>

                    <div class="px-4 pb-6">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                <span class="font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0 bg-gray-50 overflow-hidden">
                <!-- Top Navbar -->
                <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10 sticky top-0">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 bg-gray-100 p-2 rounded-md focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-extrabold tracking-tight text-gray-900">
                            <span class="hidden sm:inline">{{ config('app.name', 'Event Gallery') }}</span>
                        </a>
                    </div>
                    
                    <nav class="hidden lg:flex items-center gap-1">
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-semibold @if(request()->routeIs('admin.dashboard')) text-indigo-700 bg-indigo-50 @else text-gray-600 hover:text-gray-900 hover:bg-gray-100 @endif">Dashboard</a>
                        <a href="{{ route('admin.events.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold @if(request()->routeIs('admin.events.*')) text-indigo-700 bg-indigo-50 @else text-gray-600 hover:text-gray-900 hover:bg-gray-100 @endif">Events</a>
                        <a href="{{ route('admin.photos.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold @if(request()->routeIs('admin.photos.*')) text-indigo-700 bg-indigo-50 @else text-gray-600 hover:text-gray-900 hover:bg-gray-100 @endif">Photos</a>
                        <a href="{{ route('profile.edit') }}" class="px-3 py-2 rounded-lg text-sm font-semibold @if(request()->routeIs('profile.*')) text-indigo-700 bg-indigo-50 @else text-gray-600 hover:text-gray-900 hover:bg-gray-100 @endif">Profile</a>
                    </nav>

                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline text-sm text-gray-500">{{ auth()->user()->email }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 md:p-8">
                    @if (session('success'))
                        <div class="mb-6 border-l-4 border-green-500 bg-green-50 text-green-800 p-4 rounded-r shadow-sm flex animate-bounce-once">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 border-l-4 border-red-500 bg-red-50 text-red-800 p-4 rounded-r shadow-sm flex animate-bounce-once">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
            
            <!-- Mobile sidebar backdrop -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 md:hidden" style="display: none;"></div>
        </div>
    </body>
</html>
