{{-- resources/views/livewire/appointments/modals/appointment-detail.blade.php --}}
<div x-data="{ show: false }" x-init="$nextTick(() => show = true)" x-show="show" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" @close-modal.window="show = false" class="modal-backdrop"
    wire:click.self="closeModal">
    <div x-show="show" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2" class="modal" role="dialog" aria-modal="true"
        aria-labelledby="modal-title-detail">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title-detail">

            <div class="modal__header">
                <h2 class="modal__title" id="modal-title-detail">Detalle de cita</h2>
                <button wire:click="closeModal" class="modal__close" aria-label="Cerrar">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M2 2l12 12M14 2L2 14" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <div class="modal__body">
                <div class="detail-grid">
                    {{-- Cliente --}}
                    <div class="detail-section">
                        <p class="detail-label">Cliente</p>
                        <div class="cell-person">
                            <div class="avatar">{{ strtoupper(substr($appt->customer->name, 0, 2)) }}</div>
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
                            <div class="avatar avatar--pro">{{ strtoupper(substr($appt->user->name, 0, 2)) }}</div>
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

                    @if ($appt->notes)
                        <div class="detail-section detail-section--full">
                            <p class="detail-label">Notas</p>
                            <p class="detail-notes">{{ $appt->notes }}</p>
                        </div>
                    @endif

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
                    <button wire:click="confirmAndClose({{ $appt->id }})" wire:loading.attr="disabled"
                        wire:target="confirmAndClose({{ $appt->id }})" class="btn btn--success">
                        <span wire:loading.remove wire:target="confirmAndClose({{ $appt->id }})">
                            Confirmar cita
                        </span>
                        <span wire:loading wire:target="confirmAndClose({{ $appt->id }})"
                            class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                                <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.5"
                                    stroke-dasharray="20" stroke-dashoffset="10" stroke-linecap="round" />
                            </svg>
                            Confirmando…
                        </span>
                    </button>
                @endif

                @if (in_array($appt->status, ['pending', 'confirmed']))
                    <button wire:click="openCancelAndClose({{ $appt->id }})" wire:loading.attr="disabled"
                        wire:target="openCancelAndClose({{ $appt->id }})" class="btn btn--danger">
                        <span wire:loading.remove wire:target="openCancelAndClose({{ $appt->id }})">
                            Cancelar cita
                        </span>
                        <span wire:loading wire:target="openCancelAndClose({{ $appt->id }})"
                            class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                                <circle cx="7" cy="7" r="5.5" stroke="currentColor"
                                    stroke-width="1.5" stroke-dasharray="20" stroke-dashoffset="10"
                                    stroke-linecap="round" />
                            </svg>
                            Procesando…
                        </span>
                    </button>
                @endif

                <button wire:click="closeModal" wire:loading.attr="disabled" class="btn btn--secondary">
                    Cerrar
                </button>

            </div>
        </div>
    </div>
