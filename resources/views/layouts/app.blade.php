<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ScreenTime') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { background-color: #0f1117; color: #e5e7eb; font-family: 'Inter', sans-serif; }
            .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
            .scrollbar-hide::-webkit-scrollbar { display: none; }
            .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
            .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen bg-[#0f1117]">
            @include('layouts.navigation')

            <main>
                {{ $slot }}
            </main>

            <footer class="bg-[#0a0b0f] border-t border-gray-800/50 py-8 mt-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🎬</span>
                            <span class="text-sm font-bold text-white">ScreenTime</span>
                        </div>
                        <div class="flex items-center gap-6 text-xs text-gray-500">
                            <span>&copy; {{ date('Y') }} ScreenTime</span>
                            <a href="https://www.themoviedb.org/" target="_blank" class="hover:text-gray-300 transition">Powered by TMDB</a>
                        </div>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('browse') }}" class="text-xs text-gray-500 hover:text-white transition">Browse</a>
                            <a href="{{ route('search') }}" class="text-xs text-gray-500 hover:text-white transition">Search</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        @stack('scripts')
    </body>
</html>
