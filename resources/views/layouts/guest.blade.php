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
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center relative overflow-hidden">

            {{-- Background Decoration --}}
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-indigo-600/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-600/10 rounded-full blur-3xl"></div>
            </div>

            {{-- Logo --}}
            <div class="relative z-10 mb-8">
                <a href="/" class="flex items-center gap-3">
                    <span class="text-3xl">🎬</span>
                    <span class="text-2xl font-bold text-white">ScreenTime</span>
                </a>
            </div>

            {{-- Card --}}
            <div class="relative z-10 w-full max-w-md mx-4 bg-gray-900/80 backdrop-blur-md border border-gray-800 rounded-2xl shadow-2xl p-8">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <div class="relative z-10 mt-6">
                <a href="/" class="text-gray-500 hover:text-gray-300 text-xs transition">← Back to home</a>
            </div>
        </div>
    </body>
</html>
