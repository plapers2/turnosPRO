{{-- resources/views/livewire/appointments/modals/cancel-confirm.blade.php --}}
<div wire:click.self="closeCancelModal"
    class="fixed inset-0 z-[70] flex items-center justify-center
           bg-black/40 backdrop-blur-[2px] p-4">
    <div role="dialog" aria-modal="true" aria-labelledby="modal-title-cancel"
        class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30
               shadow-[0_8px_32px_rgba(0,0,0,0.12)] w-full max-w-sm flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/20">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-xl bg-[#FCEBEB] text-[#A32D2D]
                            flex items-center justify-center border border-[#F7C1C1]">
                    <svg width="15" height="15" viewBox="0 0 14 14" fill="none">
                        <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" />
                    </svg>
                </div>
                <h2 id="modal-title-cancel" class="text-[15px] font-semibold text-on-surface">
                    Cancelar cita
                </h2>
            </div>
            <button wire:click="closeCancelModal" aria-label="Cerrar"
                class="w-7 h-7 flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150">
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                    <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 flex flex-col gap-4">
            <div
                class="flex items-start gap-3 bg-[#FAEEDA] border border-[#FAC775]
                        rounded-xl px-4 py-3">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" class="flex-shrink-0 mt-0.5">
                    <path d="M8 2L14.5 13.5H1.5L8 2Z" stroke="#854F0B" stroke-width="1.5" stroke-linejoin="round" />
                    <path d="M8 6.5v3M8 11.5v.5" stroke="#854F0B" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                <p class="text-[13px] text-[#854F0B] leading-snug">
                    ¿Estás seguro de que quieres cancelar esta cita?
                    Esta acción no se puede deshacer.
                </p>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="cancel-reason" class="text-[11.5px] font-semibold text-on-surface-variant">
                    Motivo de cancelación
                </label>
                <textarea id="cancel-reason" wire:model="cancellationReason" rows="3"
                    placeholder="Ej: Cliente solicitó reprogramar…"
                    class="w-full bg-surface-container border rounded-xl px-3 py-2.5
                           text-[13px] text-on-surface placeholder:text-on-surface-variant/50
                           outline-none resize-none transition-all duration-150
                           {{ $errors->has('cancellationReason')
                               ? 'border-error/60 focus:ring-2 focus:ring-error/20'
                               : 'border-outline-variant/30 focus:ring-2 focus:ring-primary/20' }}"></textarea>
                @error('cancellationReason')
                    <p class="text-[11.5px] text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-outline-variant/20">
            <button wire:click="cancelAppointment" wire:loading.attr="disabled" wire:target="cancelAppointment"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-semibold
           bg-[#FCEBEB] text-[#A32D2D] border border-[#F7C1C1]
           hover:bg-[#E24B4A] hover:text-white hover:border-[#E24B4A]
           disabled:opacity-50 transition-colors duration-150">
                <span wire:loading.remove wire:target="cancelAppointment" class="inline-flex items-center gap-1.5">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                        <path d="M2 2l9 9M11 2L2 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    Sí, cancelar
                </span>
                <span wire:loading wire:target="cancelAppointment" class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                        <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.5"
                            stroke-dasharray="20" stroke-dashoffset="10" stroke-linecap="round" />
                    </svg>
                    Cancelando…
                </span>
            </button>
            <button wire:click="closeCancelModal"
                class="inline-flex items-center px-4 py-2 rounded-xl text-[13px] font-semibold
                       bg-surface-container border border-outline-variant/20 text-on-surface-variant
                       hover:bg-surface-container-high transition-colors duration-150">
                Volver
            </button>
        </div>
    </div>
</div>
