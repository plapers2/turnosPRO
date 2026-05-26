@props(['icono', 'titulo', 'mensaje', 'mensajeEmpleado' => null, 'textoBoton' => null, 'ruta' => null])

<header
    class="relative mx-4 sm:mx-6 lg:mx-8 mt-6 lg:mt-10 mb-4 overflow-hidden
           rounded-2xl border border-outline-variant/30
           bg-surface-container-lowest px-4 sm:px-6 py-5 sm:py-7
           flex flex-col lg:flex-row
           items-start lg:items-center
           justify-between gap-5
           shadow-[0_1px_8px_rgba(95,94,90,0.06)]">

    {{-- Acento izquierdo --}}
    <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-primary rounded-l-2xl"></div>

    {{-- Ícono + texto --}}
    <div class="flex items-start sm:items-center gap-4 pl-2 w-full lg:w-auto">

        <div
            class="flex h-[42px] w-[42px] shrink-0 items-center justify-center
                   rounded-xl border border-primary-fixed-dim/40
                   bg-primary-fixed/20 text-primary">

            <span class="material-symbols-outlined text-[20px]">
                {{ $icono }}
            </span>
        </div>

        <div class="flex flex-col gap-1 min-w-0">

            {{-- Título --}}
            <h2
                class="text-[16px] sm:text-[17px] font-semibold
                       leading-tight tracking-tight text-on-surface
                       break-words">
                {{ $titulo }}
            </h2>

            {{-- Mensaje --}}
            <p class="text-[12px] sm:text-[13px]
                       text-on-surface-variant break-words">
                {{ auth()->user()->hasRole('admin') ? $mensaje : $mensajeEmpleado }}
            </p>

        </div>
    </div>

    {{-- Lado derecho --}}
    @role('admin')
        @if ($textoBoton && $ruta)
            <div class="w-full lg:w-auto">

                <a href="{{ route($ruta . '.create') }}"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2
                           rounded-xl bg-primary px-5 py-2.5
                           text-[13px] font-semibold text-on-primary
                           shadow-sm transition-opacity hover:opacity-90">

                    <span class="material-symbols-outlined text-[16px]">
                        add
                    </span>

                    {{ $textoBoton }}
                </a>

            </div>
        @endif
    @endrole

</header>
