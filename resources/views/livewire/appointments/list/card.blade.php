{{-- resources/views/livewire/appointments/list/card.blade.php --}}
{{-- Props: $appt (Appointment) --}}
<article class="appt-card" wire:key="card-{{ $appt->id }}">

    <div class="appt-card__top">
        <div class="cell-person">
            <div class="avatar">
                {{ strtoupper(substr($appt->customer->name, 0, 2)) }}
            </div>
            <div>
                <p class="cell-person__name">{{ $appt->customer->name }}</p>
                <p class="cell-person__sub">{{ $appt->customer->phone }}</p>
            </div>
        </div>
        <span class="status-badge status-badge--{{ $appt->status }}">
            {{ __('appt.status.' . $appt->status) }}
        </span>
    </div>

    <div class="appt-card__body">
        <div class="appt-card__meta">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                <circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.5"/>
                <path d="M6.5 4v3l2 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            {{ $appt->start_time->format('d/m/Y H:i') }} – {{ $appt->end_time->format('H:i') }}
        </div>
        <div class="appt-card__meta">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                <circle cx="6.5" cy="4.5" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                <path d="M1.5 11.5c0-2.76 2.24-5 5-5s5 2.24 5 5"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            {{ $appt->user->name }}
        </div>
        <div class="services-list">
            @foreach ($appt->services->take(3) as $svc)
                <span class="service-tag">{{ $svc->name }}</span>
            @endforeach
        </div>
    </div>

    <div class="appt-card__actions">
        <button wire:click="viewAppointment({{ $appt->id }})" class="card-btn">
            Ver
        </button>

        @if ($appt->status === 'pending')
            <button
                wire:click="confirmAppointment({{ $appt->id }})"
                class="card-btn card-btn--success"
            >
                Confirmar
            </button>
        @endif

        @if (in_array($appt->status, ['pending', 'confirmed']))
            <button
                wire:click="openCancelModal({{ $appt->id }})"
                class="card-btn card-btn--danger"
            >
                Cancelar
            </button>
        @endif
    </div>

</article>
