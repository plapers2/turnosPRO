{{-- resources/views/livewire/appointments/list/table-row.blade.php --}}
{{-- Props: $appt (Appointment) --}}
<tr class="appt-table__row" wire:key="row-{{ $appt->id }}">

    {{-- Cliente --}}
    <td>
        <div class="cell-person">
            <div class="avatar avatar--sm">
                {{ strtoupper(substr($appt->customer->name, 0, 2)) }}
            </div>
            <div>
                <p class="cell-person__name">{{ $appt->customer->name }}</p>
                <p class="cell-person__sub">{{ $appt->customer->phone }}</p>
            </div>
        </div>
    </td>

    {{-- Profesional --}}
    <td>
        <div class="cell-person">
            <div class="avatar avatar--sm avatar--pro">
                {{ strtoupper(substr($appt->user->name, 0, 2)) }}
            </div>
            <span>{{ $appt->user->name }}</span>
        </div>
    </td>

    {{-- Servicios --}}
    <td>
        <div class="services-list">
            @foreach ($appt->services->take(2) as $svc)
                <span class="service-tag">{{ $svc->name }}</span>
            @endforeach
            @if ($appt->services->count() > 2)
                <span class="service-tag service-tag--more">
                    +{{ $appt->services->count() - 2 }}
                </span>
            @endif
        </div>
    </td>

    {{-- Fecha y hora --}}
    <td>
        <p class="cell-date">{{ $appt->start_time->format('d/m/Y') }}</p>
        <p class="cell-time">
            {{ $appt->start_time->format('H:i') }} – {{ $appt->end_time->format('H:i') }}
        </p>
    </td>

    {{-- Duración --}}
    <td>
        <span class="cell-duration">
            {{ $appt->start_time->diffInMinutes($appt->end_time) }} min
        </span>
    </td>

    {{-- Estado --}}
    <td>
        <span class="status-badge status-badge--{{ $appt->status }}">
            {{ __('appt.status.' . $appt->status) }}
        </span>
    </td>

    {{-- Acciones --}}
    <td>
        <div class="action-btns">
            <button
                wire:click="viewAppointment({{ $appt->id }})"
                class="action-btn action-btn--view"
                title="Ver detalle"
            >
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <circle cx="7" cy="7" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M1 7s2-4.5 6-4.5S13 7 13 7s-2 4.5-6 4.5S1 7 1 7z"
                        stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>

            @if ($appt->status === 'pending')
                <button
                    wire:click="confirmAppointment({{ $appt->id }})"
                    class="action-btn action-btn--confirm"
                    title="Confirmar"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M2 7l3.5 3.5L12 4" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            @endif

            @if (in_array($appt->status, ['pending', 'confirmed']))
                <button
                    wire:click="openCancelModal({{ $appt->id }})"
                    class="action-btn action-btn--cancel"
                    title="Cancelar"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M2 2l10 10M12 2L2 12" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
            @endif
        </div>
    </td>

</tr>
