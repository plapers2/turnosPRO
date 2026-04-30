{{-- resources/views/livewire/appointments/calendar/nav.blade.php --}}
{{-- Props: $calendarMonth (string 'Y-m') --}}
<div class="cal-nav">
    <button wire:click="previousMonth" class="cal-nav__btn" title="Mes anterior">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M10 4L6 8l4 4" stroke="currentColor" stroke-width="1.5"
                stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <span class="cal-nav__month">
        {{ \Carbon\Carbon::parse($calendarMonth . '-01')->locale('es')->isoFormat('MMMM YYYY') }}
    </span>

    <button wire:click="nextMonth" class="cal-nav__btn" title="Mes siguiente">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5"
                stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
</div>
