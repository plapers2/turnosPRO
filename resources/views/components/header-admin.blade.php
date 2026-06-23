@props(['icono', 'titulo', 'mensaje', 'mensajeEmpleado' => null, 'textoBoton' => null, 'ruta' => null])

<header
    class="relative mx-4 sm:mx-6 lg:mx-8 mt-6 lg:mt-10 mb-4
           rounded-2xl border border-outline-variant/30
           bg-surface-container-lowest
           pl-5 pr-4 sm:pr-6 py-5 sm:py-6
           flex flex-col sm:flex-row
           items-start sm:items-center
           justify-between gap-4 sm:gap-5
           shadow-[0_1px_8px_rgba(95,94,90,0.06)]">

    {{-- Acento izquierdo --}}
    <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-primary rounded-l-2xl"></div>

    {{-- Ícono + texto --}}
    <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1 pl-2">
        <div
            class="flex h-[40px] w-[40px] sm:h-[42px] sm:w-[42px] shrink-0 items-center justify-center
                   rounded-xl border border-primary-fixed-dim/40
                   bg-primary-fixed/20 text-primary">
            <span class="material-symbols-outlined text-[20px]">
                {{ $icono }}
            </span>
        </div>
        <div class="flex flex-col gap-0.5 min-w-0">
            <h2 class="text-[15px] sm:text-[16px] font-semibold
                       leading-tight tracking-tight text-on-surface">
                {{ $titulo }}
            </h2>
            <p class="text-[12px] sm:text-[13px] text-on-surface-variant leading-snug">
                {{ auth()->user()->hasRole('admin') ? $mensaje : $mensajeEmpleado }}
            </p>
        </div>
    </div>

    {{-- Lado derecho --}}
    @role('admin')
        @if ($textoBoton && $ruta)
            <div class="w-full sm:w-auto shrink-0 pl-2 sm:pl-0">
                <a href="{{ route($ruta . '.create') }}"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2
                           rounded-xl bg-primary px-4 sm:px-5 py-2.5
                           text-[13px] font-semibold text-on-primary
                           shadow-sm transition-opacity hover:opacity-90 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    {{ $textoBoton }}
                </a>
            </div>
        @endif
    @endrole
</header>
