{{-- resources/views/livewire/appointments/calendar/nav.blade.php --}}
{{-- Props: $calendarMonth (string 'Y-m') --}}
<div class="flex items-center justify-between px-1 mb-3">

    {{-- Navegación de mes --}}
    <div class="flex items-center gap-2">
        <button wire:click="previousMonth"
            class="w-8 h-8 flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150"
            title="Mes anterior">
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                <path d="M10 4L6 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>

        <span class="text-sm font-semibold text-primary capitalize min-w-[140px] text-center">
            {{ \Carbon\Carbon::parse($calendarMonth . '-01')->locale('es')->isoFormat('MMMM YYYY') }}
        </span>

        <button wire:click="nextMonth"
            class="w-8 h-8 flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150"
            title="Mes siguiente">
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    {{-- Leyenda de estados --}}
    <div class="hidden sm:flex items-center gap-x-4 gap-y-1 flex-wrap justify-end">
        @foreach ([['color' => '#BA7517', 'label' => 'Pendiente'], ['color' => '#1D9E75', 'label' => 'Confirmada'], ['color' => '#378ADD', 'label' => 'Completada'], ['color' => '#E24B4A', 'label' => 'Cancelada']] as $s)
            <span class="inline-flex items-center gap-1.5 text-xs text-on-surface-variant">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $s['color'] }}"></span>
                {{ $s['label'] }}
            </span>
        @endforeach
    </div>

</div>
