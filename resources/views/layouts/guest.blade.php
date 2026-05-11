<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BMI System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Outfit', sans-serif; }

            @keyframes fadeSlideUp {
                from { opacity: 0; transform: translateY(12px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-entry {
                animation: fadeSlideUp 0.4s ease-out forwards;
                opacity: 0;
            }

            .delay-1 { animation-delay: 0.05s; }
            .delay-2 { animation-delay: 0.1s; }
            .delay-3 { animation-delay: 0.15s; }
            .delay-4 { animation-delay: 0.2s; }
            .delay-5 { animation-delay: 0.25s; }
        </style>
    </head>
    <body class="antialiased bg-slate-900">
        <!-- Toast Notification -->
        @if(session('success') || session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-[-1rem]"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-[-1rem]"
                 class="fixed top-5 left-1/2 -translate-x-1/2 z-50 max-w-lg w-full px-4">
                <div class="bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('success') ?? session('status') }}</p>
                    <button @click="show = false" class="ml-auto text-white/70 hover:text-white shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="min-h-screen flex flex-col items-center {{ request()->routeIs('register') ? 'justify-start pt-8' : 'justify-center' }} px-4 py-8">

            <div class="w-full {{ request()->routeIs('register') ? 'max-w-xl' : 'max-w-md' }}">

                <!-- Logos + Title -->
                <div class="text-center {{ request()->routeIs('register') ? 'mb-4' : 'mb-6' }} animate-entry delay-1">
                    <div class="flex items-center justify-center {{ request()->routeIs('register') ? 'gap-3 mb-2' : 'gap-5 mb-4' }}">
                        <img src="{{ asset('images/pnp-logo.png') }}" alt="PNP Logo" class="{{ request()->routeIs('register') ? 'w-12 h-12' : 'w-16 h-16 sm:w-20 sm:h-20' }} object-contain">
                        <img src="{{ asset('images/luppo-logo.png') }}" alt="LUPPO Logo" class="{{ request()->routeIs('register') ? 'w-12 h-12' : 'w-16 h-16 sm:w-20 sm:h-20' }} object-contain">
                    </div>
                    <h1 class="{{ request()->routeIs('register') ? 'text-xl' : 'text-2xl sm:text-3xl' }} font-bold text-white tracking-tight">
                        BMI Monitoring System
                    </h1>
                    <p class="text-sm text-slate-400 mt-1">
                        La Union Police Provincial Office
                    </p>
                </div>

                <!-- Card -->
                <div class="bg-white rounded-2xl shadow-lg {{ request()->routeIs('register') ? 'p-6 sm:p-8' : 'p-8 sm:p-10' }} animate-entry delay-2">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <p class="text-center text-xs text-slate-500 mt-6 animate-entry delay-5">
                    Philippine National Police &mdash; La Union PPO
                </p>

            </div>
        </div>
    </body>
</html>
