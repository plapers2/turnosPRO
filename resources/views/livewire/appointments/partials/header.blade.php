{{-- resources/views/livewire/appointments/partials/header.blade.php --}}
{{-- Props: $total (int) --}}
<header class="appt-header">
    <div class="appt-header__title">
        <h1>Citas</h1>
        <span class="appt-header__badge">{{ $total }}</span>
    </div>

    <div class="view-toggle">
        <button @click="view = 'list'" class="view-toggle__btn"
            :class="{ 'view-toggle__btn--active': view === 'list' }" title="Vista lista">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="3" width="14" height="2" rx="1" fill="currentColor" />
                <rect x="1" y="7" width="14" height="2" rx="1" fill="currentColor" />
                <rect x="1" y="11" width="14" height="2" rx="1" fill="currentColor" />
            </svg>
            <span>Lista</span>
        </button>

        <button @click="view = 'calendar'" class="view-toggle__btn"
            :class="{ 'view-toggle__btn--active': view === 'calendar' }" title="Vista calendario">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="2" width="14" height="13" rx="2" stroke="currentColor" stroke-width="1.5"
                    fill="none" />
                <path d="M1 6h14" stroke="currentColor" stroke-width="1.5" />
                <path d="M5 1v2M11 1v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <span>Calendario</span>
        </button>
    </div>
</header>
