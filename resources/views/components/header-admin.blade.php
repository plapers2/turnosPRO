@props(['icono', 'titulo', 'mensaje', 'mensajeEmpleado' => null, 'textoBoton' => null, 'ruta' => null])

<header
    class="relative mx-8 mt-10 mb-4 overflow-hidden rounded-2xl border border-outline-variant/30
               bg-surface-container-lowest px-6 py-5
               flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4
               shadow-[0_1px_8px_rgba(95,94,90,0.06)]">

    {{-- Acento izquierdo --}}
    <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-primary rounded-l-2xl"></div>

    {{-- Ícono + texto --}}
    <div class="flex items-center gap-4 pl-2">
        <div
            class="flex h-[42px] w-[42px] shrink-0 items-center justify-center
                    rounded-xl border border-primary-fixed-dim/40 bg-primary-fixed/20 text-primary">
            <span class="material-symbols-outlined text-[20px]">{{ $icono }}</span>
        </div>
        <div class="flex flex-col gap-0.5">
            <h2 class="text-[17px] font-semibold leading-tight tracking-tight text-on-surface">
                {{ $titulo }}
            </h2>
            <p class="text-[13px] text-on-surface-variant">
                {{ auth()->user()->hasRole('admin') ? $mensaje : $mensajeEmpleado }}
            </p>
        </div>
    </div>

    {{-- Lado derecho --}}
    @role('admin')
        <div class="flex items-center gap-4 shrink-0">

            @if ($textoBoton && $ruta)
                <a href="{{ route($ruta . '.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl
                           bg-primary px-5 py-2.5 text-[13px] font-semibold
                           text-on-primary shadow-sm transition-opacity hover:opacity-90">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    {{ $textoBoton }}
                </a>
            @endif

        </div>
    @endrole

</header>
