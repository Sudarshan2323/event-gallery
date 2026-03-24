<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Event Gallery') }} - Capture Every Moment</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700|inter:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .font-serif { font-family: 'Playfair Display', serif; }
            .font-sans { font-family: 'Inter', sans-serif; }
            .hero-pattern {
                background-color: #ffffff;
                background-image: radial-gradient(rgba(79, 70, 229, 0.1) 1px, transparent 1px);
                background-size: 20px 20px;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-white selection:bg-indigo-500 selection:text-white">
        <!-- Navigation -->
        <nav class="absolute top-0 w-full z-10 bg-transparent py-6">
            <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
                <div class="text-2xl font-serif font-bold tracking-tighter text-indigo-950">
                    EVENT<span class="text-indigo-600">GALLERY</span>
                </div>
                <div>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('admin.dashboard') }}" class="font-medium text-gray-700 hover:text-indigo-600 transition-colors">Admin Dashboard</a>
                        @else
                            <a href="{{ route('login', [], false) }}" class="inline-flex items-center px-5 py-2.5 rounded-full bg-white text-indigo-600 font-semibold text-sm shadow-sm ring-1 ring-inset ring-indigo-100 hover:bg-gray-50 transition-all">
                                Organizer Login
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative isolate pt-14 hero-pattern min-h-screen flex items-center justify-center overflow-hidden">
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
            
            <div class="py-24 sm:py-32 lg:pb-40 w-full">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <div class="hidden sm:mb-8 sm:flex sm:justify-center">
                            <div class="relative rounded-full px-3 py-1 text-sm leading-6 text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                                Announcing our new live slideshow feature. <a href="#features" class="font-semibold text-indigo-600"><span class="absolute inset-0" aria-hidden="true"></span>Read more <span aria-hidden="true">&rarr;</span></a>
                            </div>
                        </div>
                        <h1 class="text-5xl font-serif tracking-tight text-gray-900 sm:text-7xl">Capture Every <br><span class="text-indigo-600 italic">Beautiful Moment</span></h1>
                        <p class="mt-6 text-lg leading-8 text-gray-600">The premier platform for event organizers and photographers to instantly share photos with guests via QR codes with real-time live sync.</p>
                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            <a href="#demo" class="rounded-full bg-indigo-600 px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all transform hover:-translate-y-1">
                                Get Started
                            </a>
                            <a href="#features" class="text-sm font-semibold leading-6 text-gray-900 flex items-center group">
                                Learn more <span class="ml-2 group-hover:translate-x-1 transition-transform" aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-16 flow-root sm:mt-24 relative">
                        <!-- Abstract floating elements -->
                        <div class="absolute -right-12 -top-12 w-24 h-24 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                        <div class="absolute -left-12 bottom-12 w-32 h-32 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                        <div class="absolute left-1/2 -bottom-12 w-28 h-28 bg-pink-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>

                        <div class="-m-2 rounded-2xl p-2 ring-1 ring-inset ring-gray-900/10 lg:-m-4 lg:rounded-3xl lg:p-4 bg-white/50 backdrop-blur-xl relative z-10 shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=2069&q=80" alt="App screenshot" class="rounded-xl shadow-2xl ring-1 ring-gray-900/10 object-cover w-full h-[30vh] sm:h-[40vh] md:h-[50vh]">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </main>

        <!-- Features Section -->
        <section id="features" class="py-24 bg-white sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-indigo-600 tracking-wide uppercase">Share Faster</h2>
                    <p class="mt-2 text-3xl font-serif font-bold tracking-tight text-gray-900 sm:text-4xl">Everything you need to share event photos</p>
                    <p class="mt-6 text-lg leading-8 text-gray-600">Designed for professional photographers, event planners, and couples who want to easily distribute high-quality photos instantly.</p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        <div class="flex flex-col">
                            <dt class="flex md:flex-col items-center md:items-start gap-4 md:gap-y-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white flex-shrink-0">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                                    </svg>
                                </div>
                                <div class="text-xl font-bold font-serif leading-7 text-gray-900">QR Code Access</div>
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600">
                                <p class="flex-auto">Generate unique QR codes for an entire event, or granular QR codes per photo. Guests just scan directly from your slideshow screens to download instantly.</p>
                            </dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="flex md:flex-col items-center md:items-start gap-4 md:gap-y-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white flex-shrink-0">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                </div>
                                <div class="text-xl font-bold font-serif leading-7 text-gray-900">Real-time WebSocket Sync</div>
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600">
                                <p class="flex-auto">Powered by Laravel Reverb, guests visiting the digital gallery or watching the live slideshow will see new photos appear instantly as you upload them, without refreshing.</p>
                            </dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="flex md:flex-col items-center md:items-start gap-4 md:gap-y-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white flex-shrink-0">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                                    </svg>
                                </div>
                                <div class="text-xl font-bold font-serif leading-7 text-gray-900">Advanced Analytics</div>
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600">
                                <p class="flex-auto">Track engagement precisely. See exactly how many times an event has been viewed, which photos get the most QR scans, and the total download counts.</p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <footer class="bg-gray-50 border-t border-gray-100 py-12">
            <div class="max-w-7xl mx-auto px-6 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Event Gallery Platform. Built with Laravel 12.
            </div>
        </footer>
    </body>
</html>
