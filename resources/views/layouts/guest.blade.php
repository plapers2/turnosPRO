<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />

    <title>{{ config('app.name', 'Login - TurnosPro') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface text-on-surface flex flex-col min-h-screen items-center justify-center p-6 bg-pattern">
    <div class="w-full max-w-[480px] flex flex-col gap-8">
        <div
            class="bg-surface-container-lowest rounded-xl p-10 flex flex-col gap-8 shadow-[0px_12px_32px_rgba(15,110,86,0.08)] relative overflow-hidden">
            <!-- Branding Header -->
            <div class="flex flex-col items-center text-center gap-3">
                <div
                    class="w-16 h-16 bg-primary-container text-on-primary-container rounded-lg flex items-center justify-center mb-2">
                    <span class="material-symbols-outlined text-4xl" data-icon="event_available">event_available</span>
                </div>
                <h1 class="text-display-lg font-bold tracking-tight text-primary leading-tight">TurnosPro</h1>
                <p class="text-secondary font-medium tracking-wide">Gestiona tus negocios eficientemente </p>
            </div>

            <div class="">
                {{ $slot }}
            </div>
        </div>

    </div>

</body>

</html>
