{{-- resources/views/livewire/appointments/calendar/nav.blade.php --}}
<div class="flex items-center justify-between px-1 mb-3">

    <div id="cal-nav" class="flex items-center gap-1.5">
        <button id="cal-prev"
            class="w-8 h-8 flex items-center justify-center rounded-xl
                   bg-surface-container border border-outline-variant/30
                   text-on-surface-variant hover:bg-surface-container-high
                   transition-colors duration-150"
            title="Anterior">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M10 4L6 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>

        <span id="cal-title"
            class="text-[13px] font-semibold text-primary capitalize min-w-[160px] text-center
                   bg-surface-container px-3 py-1.5 rounded-xl border border-outline-variant/30">
            {{ \Carbon\Carbon::parse($calendarMonth . '-01')->locale('es')->isoFormat('MMMM YYYY') }}
        </span>

        <button id="cal-next"
            class="w-8 h-8 flex items-center justify-center rounded-xl
                   bg-surface-container border border-outline-variant/30
                   text-on-surface-variant hover:bg-surface-container-high
                   transition-colors duration-150"
            title="Siguiente">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    <div class="hidden sm:flex items-center gap-x-4 gap-y-1 flex-wrap justify-end">
        @foreach ([['color' => '#0F6E56', 'bg' => '#E1F5EE', 'label' => 'Confirmada'], ['color' => '#185FA5', 'bg' => '#E6F1FB', 'label' => 'Completada'], ['color' => '#A32D2D', 'bg' => '#FCEBEB', 'label' => 'Cancelada']] as $s)
            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-on-surface-variant">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $s['color'] }}"></span>
                {{ $s['label'] }}
            </span>
        @endforeach
    </div>

</div>
