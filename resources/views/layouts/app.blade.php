<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- 토스트 --}}
        <div
            x-data
            x-show="$store.toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            :class="{
                'bg-green-100 text-green-800 border-green-200': $store.toast.type === 'success',
                'bg-red-100 text-red-800 border-red-200': $store.toast.type === 'error',
            }"
            class="fixed bottom-6 right-6 text-sm px-5 py-3 rounded-xl shadow-lg z-50 border"
            style="display: none;"
        >
            <span x-text="$store.toast.message"></span>
        </div>

        <script>
            window.__CART_COUNT__ = {{ $cartCount ?? 0 }};
        </script>

        @if (session('error'))
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.store('toast').show("{{ session('error') }}", 'error');
                });
            </script>
        @endif
    </body>
</html>
