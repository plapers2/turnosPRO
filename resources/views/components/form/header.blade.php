@props(['icono', 'titulo', 'subtitulo', 'ruta'])
<header class="px-3 sm:px-6 lg:px-8 mt-6 sm:mt-8 lg:mt-10 mb-4 sm:mb-6 flex flex-wrap items-center justify-between gap-y-2">
    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
        <a href="{{ route($ruta . '.index') }}"
            class="flex items-center gap-1.5 sm:gap-2 text-sm text-on-surface-variant hover:text-primary transition shrink-0">
            <span class="material-symbols-outlined text-[18px]">{{ $icono }}</span>
            <span class="hidden xs:inline">Volver</span>
        </a>
        <div class="h-6 w-px bg-outline-variant/40 shrink-0"></div>
        <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-primary tracking-tight leading-tight truncate">
            {{ $titulo }}
        </h1>
    </div>
    <div class="text-xs sm:text-sm text-on-surface-variant pl-0 sm:pl-2 w-full sm:w-auto text-left sm:text-right">
        {{ $subtitulo }}
    </div>
</header>
