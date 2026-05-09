    <!DOCTYPE html>
    <html data-theme="cmyk" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <script>
            document.documentElement.style.visibility = 'hidden';
            document.addEventListener('alpine:init', () => {
                document.documentElement.style.visibility = 'visible';
            });
        </script>
        @livewireStyles
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'TurnosPro') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap"
            rel="stylesheet" />
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet" />

        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
            rel="stylesheet" />
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet" />


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @stack('styles')
    </head>

    <body
        class="bg-surface text-on-surface h-screen flex overflow-hidden selection:bg-primary-container selection:text-on-primary-container">

        @include('layouts.side-bar')
        <main class="md:ml-60 flex-1 h-screen overflow-y-auto bg-surface relative">
            @include('layouts.header')
            {{ $slot }}
        </main>

        <!-- Scripts -->
        <x-toast />

        @if (session('success') || $errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => {

                        @if (session('success'))
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    type: 'success',
                                    message: "{{ session('success') }}"
                                }
                            }))
                        @endif

                    }, 100)
                })
            </script>
        @endif
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        @stack('scripts')
        @livewireScripts
    </body>

    </html>
