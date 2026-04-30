{{-- resources/views/livewire/appointments/modals/appointment-detail.blade.php --}}
{{-- Props: $appt (Appointment con relaciones cargadas) --}}
<div class="modal-backdrop" wire:click.self="closeModal">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title-detail">

        <div class="modal__header">
            <h2 class="modal__title" id="modal-title-detail">Detalle de cita</h2>
            <button wire:click="closeModal" class="modal__close" aria-label="Cerrar">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M2 2l12 12M14 2L2 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="modal__body">
            <div class="detail-grid">

                {{-- Cliente --}}
                <div class="detail-section">
                    <p class="detail-label">Cliente</p>
                    <div class="cell-person">
                        <div class="avatar">
                            {{ strtoupper(substr($appt->customer->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="cell-person__name">{{ $appt->customer->name }}</p>
                            <p class="cell-person__sub">{{ $appt->customer->email }}</p>
                            <p class="cell-person__sub">{{ $appt->customer->phone }}</p>
                        </div>
                    </div>
                </div>

                {{-- Profesional --}}
                <div class="detail-section">
                    <p class="detail-label">Profesional</p>
                    <div class="cell-person">
                        <div class="avatar avatar--pro">
                            {{ strtoupper(substr($appt->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="cell-person__name">{{ $appt->user->name }}</p>
                            <p class="cell-person__sub">{{ $appt->user->email }}</p>
                        </div>
                    </div>
                </div>

                {{-- Fecha y hora --}}
                <div class="detail-section">
                    <p class="detail-label">Fecha y hora</p>
                    <p class="detail-value">{{ $appt->start_time->format('d/m/Y') }}</p>
                    <p class="detail-value--sub">
                        {{ $appt->start_time->format('H:i') }} – {{ $appt->end_time->format('H:i') }}
                        ({{ $appt->start_time->diffInMinutes($appt->end_time) }} min)
                    </p>
                </div>

                {{-- Estado --}}
                <div class="detail-section">
                    <p class="detail-label">Estado</p>
                    <span class="status-badge status-badge--{{ $appt->status }}">
                        {{ __('appt.status.' . $appt->status) }}
                    </span>
                </div>

                {{-- Servicios --}}
                <div class="detail-section detail-section--full">
                    <p class="detail-label">Servicios</p>
                    <div class="services-list">
                        @foreach ($appt->services as $svc)
                            <span class="service-tag service-tag--lg">{{ $svc->name }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- Notas --}}
                @if ($appt->notes)
                    <div class="detail-section detail-section--full">
                        <p class="detail-label">Notas</p>
                        <p class="detail-notes">{{ $appt->notes }}</p>
                    </div>
                @endif

                {{-- Motivo de cancelación --}}
                @if ($appt->cancellation_reason)
                    <div class="detail-section detail-section--full">
                        <p class="detail-label">Motivo de cancelación</p>
                        <p class="detail-notes detail-notes--danger">{{ $appt->cancellation_reason }}</p>
                    </div>
                @endif

            </div>
        </div>

        <div class="modal__footer">
            @if ($appt->status === 'pending')
                <button
                    wire:click="confirmAppointment({{ $appt->id }}); closeModal()"
                    class="btn btn--success"
                >
                    Confirmar cita
                </button>
            @endif

            @if (in_array($appt->status, ['pending', 'confirmed']))
                <button
                    wire:click="openCancelModal({{ $appt->id }}); closeModal()"
                    class="btn btn--danger"
                >
                    Cancelar cita
                </button>
            @endif

            <button wire:click="closeModal" class="btn btn--secondary">Cerrar</button>
        </div>

    </div>
</div>
