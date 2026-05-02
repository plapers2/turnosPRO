{{-- resources/views/livewire/appointments/modals/complete-confirm.blade.php --}}
<div wire:click.self="closeCompleteModal"
    class="fixed inset-0 z-[70] flex items-center justify-center
           bg-black/40 backdrop-blur-[2px] p-4">
    <div role="dialog" aria-modal="true" aria-labelledby="modal-title-complete"
        class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
               shadow-lg w-full max-w-sm flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/20">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#E6F1FB] flex items-center justify-center flex-shrink-0">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" class="text-[#185FA5]">
                        <path d="M1.5 7.5l3 3 8-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <circle cx="7" cy="7" r="6" stroke="currentColor" stroke-width="1.5"
                            stroke-dasharray="2 2" />
                    </svg>
                </div>
                <h2 id="modal-title-complete" class="text-base font-semibold text-primary">
                    Completar cita
                </h2>
            </div>
            <button wire:click="closeCompleteModal" aria-label="Cerrar"
                class="w-7 h-7 flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high transition-colors duration-150">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 flex flex-col gap-4">

            <div class="flex items-start gap-3 bg-[#E6F1FB] border border-[#9EC8F0] rounded-lg px-4 py-3">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                    class="flex-shrink-0 mt-0.5 text-[#185FA5]">
                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                    <path d="M8 5v3.5M8 10.5v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                <p class="text-sm text-[#185FA5] leading-snug">
                    ¿Confirmas que el servicio fue prestado correctamente?
                    <strong class="font-semibold">Esta acción es irreversible</strong>
                    y quedará registrada en el historial del cliente.
                </p>
            </div>

            <div
                class="flex items-center gap-2.5 bg-surface-container rounded-lg px-3 py-2.5
                        border border-outline-variant/20">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                    class="text-on-surface-variant flex-shrink-0">
                    <rect x="1" y="7" width="3" height="6" rx="1" stroke="currentColor"
                        stroke-width="1.3" />
                    <rect x="5.5" y="4" width="3" height="9" rx="1" stroke="currentColor"
                        stroke-width="1.3" />
                    <rect x="10" y="1" width="3" height="12" rx="1" stroke="currentColor"
                        stroke-width="1.3" />
                </svg>
                <p class="text-xs text-on-surface-variant leading-snug">
                    La cita se contabilizará en el dashboard y en el historial del cliente.
                </p>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-outline-variant/20">
            <button wire:click="completeAppointment" wire:loading.attr="disabled" wire:target="completeAppointment"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold
                       bg-[#E6F1FB] text-[#185FA5] border border-[#9EC8F0]
                       hover:bg-[#378ADD] hover:text-white hover:border-[#378ADD]
                       disabled:opacity-50 transition-colors duration-150">
                <span wire:loading.remove wire:target="completeAppointment" class="inline-flex items-center gap-1.5">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                        <path d="M1.5 7l3 3 7-6.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Sí, completar
                </span>
                <span wire:loading wire:target="completeAppointment" class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                        <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.5"
                            stroke-dasharray="20" stroke-dashoffset="10" stroke-linecap="round" />
                    </svg>
                    Guardando…
                </span>
            </button>

            <button wire:click="closeCompleteModal" wire:loading.attr="disabled" wire:target="completeAppointment"
                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold
                       bg-surface-container border border-outline-variant/20 text-on-surface-variant
                       hover:bg-surface-container-high transition-colors duration-150">
                Cancelar
            </button>
        </div>

    </div>
</div>
