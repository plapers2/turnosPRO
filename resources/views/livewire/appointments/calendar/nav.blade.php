{{-- resources/views/livewire/appointments/calendar/nav.blade.php --}}
{{-- Los botones y el título los controla view.blade.php via JS          --}}
{{-- Este partial solo renderiza la leyenda de estados (no se re-monta) --}}

<div class="flex items-center justify-between px-1 mb-3">

    {{-- Botones e título: renderizados por JS en view.blade.php --}}
    <div id="cal-nav" class="flex items-center gap-2">
        <button id="cal-prev"
            class="w-8 h-8 flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150"
            title="Anterior">
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                <path d="M10 4L6 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>

        <span id="cal-title" class="text-sm font-semibold text-primary capitalize min-w-[160px] text-center">
        </span>

        <button id="cal-next"
            class="w-8 h-8 flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150"
            title="Siguiente">
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    {{-- Leyenda --}}
    <div class="hidden sm:flex items-center gap-x-4 gap-y-1 flex-wrap justify-end">
        @foreach ([['color' => '#BA7517', 'label' => 'Pendiente'], ['color' => '#1D9E75', 'label' => 'Confirmada'], ['color' => '#378ADD', 'label' => 'Completada'], ['color' => '#E24B4A', 'label' => 'Cancelada']] as $s)
            <span class="inline-flex items-center gap-1.5 text-xs text-on-surface-variant">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $s['color'] }}"></span>
                {{ $s['label'] }}
            </span>
        @endforeach
    </div>

</div>
