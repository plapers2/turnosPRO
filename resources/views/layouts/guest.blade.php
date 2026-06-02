<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link rel="shortcut icon" href="./calendario.png" type="image/x-icon">
    <title>{{ config('app.name', 'TurnosPro') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        .animate-fade-up {
            animation: fadeUp 0.5s ease forwards;
        }

        .delay-1 {
            animation-delay: 0.1s;
            opacity: 0;
        }

        .delay-2 {
            animation-delay: 0.2s;
            opacity: 0;
        }

        .delay-3 {
            animation-delay: 0.3s;
            opacity: 0;
        }
    </style>
</head>

<body class="bg-surface text-on-surface flex flex-col min-h-screen items-center justify-center p-6 bg-pattern">
    <div class="w-full max-w-[440px] flex flex-col gap-6">

        <!-- Logo / Branding -->
        <div class="flex flex-col items-center gap-3 animate-fade-up">
            <div class="animate-float">
                @if (file_exists(public_path('turnos-pro.png')))
                    <img src="{{ asset('turnos-pro.png') }}" alt="{{ config('app.name') }}"
                        class="h-24 w-auto object-contain drop-shadow-md" />
                @elseif(file_exists(public_path('images/logo.svg')))
                    <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}"
                        class="h-24 w-auto object-contain drop-shadow-md" />
                @else
                    {{-- Fallback: ícono + nombre --}}
                    <div
                        class="w-16 h-16 bg-primary-container text-on-primary-container rounded-2xl flex items-center justify-center shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined text-4xl"
                            style="font-variation-settings:'FILL' 1">event_available</span>
                    </div>
                @endif
            </div>
            <div class="text-center">
                <h1 class="text-2xl font-bold tracking-tight text-on-surface">{{ config('app.name', 'TurnosPro') }}</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Gestiona tus negocios eficientemente</p>
            </div>
        </div>

        <!-- Card -->
        <div
            class="bg-surface-container-lowest rounded-2xl p-8 flex flex-col gap-6 shadow-[0px_8px_40px_rgba(0,0,0,0.08)] border border-outline-variant/30 animate-fade-up delay-1">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-on-surface-variant animate-fade-up delay-2">
            © {{ date('Y') }} {{ config('app.name') }} · Todos los derechos reservados
        </p>
    </div>
</body>

</html>
