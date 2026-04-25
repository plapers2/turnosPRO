@props(['icono', 'titulo', 'mensaje', 'textoBoton', 'ruta'])
<!-- HEADER (igual que servicios) -->
<header
    class="relative bg-[#fcf9f3]/80 backdrop-blur-md border border-outline-variant/20
           rounded-xl mx-8 mt-10 mb-4 px-6 py-5 flex flex-col lg:flex-row
           items-start lg:items-center justify-between gap-4 shadow-[0_8px_20px_rgba(95,94,90,0.04)]">

    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">{{ $icono }}</span>
            </div>

            <h2 class="text-xl font-bold text-primary tracking-tight">
                {{ $titulo }}
            </h2>
        </div>

        <p class="text-sm text-on-surface-variant ml-13">
            {{ $mensaje }}
        </p>
    </div>

    <a href="{{ route($ruta . ".create") }}"
        class="flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold
               bg-primary text-white hover:bg-primary/90 transition shadow-sm">

        <span class="material-symbols-outlined text-[18px]">add</span>
        {{ $textoBoton }}
    </a>
</header>
