{{-- resources/views/livewire/appointments/modals/cancel-confirm.blade.php --}}
{{-- Sin props: usa $cancellationReason del componente padre via wire:model --}}
<div class="modal-backdrop" wire:click.self="$set('showCancelConfirm', false)">
    <div class="modal modal--sm" role="dialog" aria-modal="true" aria-labelledby="modal-title-cancel">

        <div class="modal__header">
            <h2 class="modal__title" id="modal-title-cancel">Cancelar cita</h2>
            <button
                wire:click="$set('showCancelConfirm', false)"
                class="modal__close"
                aria-label="Cerrar"
            >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M2 2l12 12M14 2L2 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="modal__body">
            <p class="cancel-warning">
                ¿Estás seguro de que quieres cancelar esta cita?
                Esta acción no se puede deshacer.
            </p>

            <label class="field-label" for="cancel-reason">
                Motivo de cancelación <span class="field-label__optional">(opcional)</span>
            </label>
            <textarea
                id="cancel-reason"
                wire:model="cancellationReason"
                rows="3"
                placeholder="Ej: Cliente solicitó reprogramar…"
                class="field-textarea"
            ></textarea>
        </div>

        <div class="modal__footer">
            <button wire:click="cancelAppointment" class="btn btn--danger">
                Sí, cancelar
            </button>
            <button wire:click="$set('showCancelConfirm', false)" class="btn btn--secondary">
                Volver
            </button>
        </div>

    </div>
</div>
