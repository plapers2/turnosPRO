@props(['icono', 'titulo', 'subtitulo'])
<header class="px-8 mt-10 mb-6 flex items-center justify-between">

    <div class="flex items-center gap-4">
        <a href="{{ route('users.index') }}"
            class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition">
            <span class="material-symbols-outlined text-[18px]">{{ $icono }}</span>
            Volver
        </a>

        <div class="h-6 w-px bg-outline-variant/40"></div>

        <h1 class="text-2xl font-bold text-primary tracking-tight">
            {{ $titulo }}
        </h1>
    </div>

    <div class="text-sm text-on-surface-variant">
        {{ $subtitulo }}
    </div>

</header>
