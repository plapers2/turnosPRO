{{-- resources/views/livewire/appointments/modals/confirm-confirm.blade.php --}}
<div wire:click.self="closeConfirmModal"
    class="fixed inset-0 z-[70] flex items-center justify-center
           bg-black/40 backdrop-blur-[2px] p-4">
    <div role="dialog" aria-modal="true" aria-labelledby="modal-title-confirm"
        class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
               shadow-lg w-full max-w-sm flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/20">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#E6F5F0] flex items-center justify-center flex-shrink-0">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" class="text-[#0F6E56]">
                        <path d="M1.5 7.5l3 3 8-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
                <h2 id="modal-title-confirm" class="text-base font-semibold text-primary">
                    Confirmar cita
                </h2>
            </div>
            <button wire:click="closeConfirmModal" aria-label="Cerrar"
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

            <div class="flex items-start gap-3 bg-[#E6F5F0] border border-[#8ECFBB] rounded-lg px-4 py-3">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                    class="flex-shrink-0 mt-0.5 text-[#0F6E56]">
                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                    <path d="M5 8.5l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <p class="text-sm text-[#0F6E56] leading-snug">
                    ¿Confirmas esta cita?
                    Se notificará al cliente y quedará registrada como
                    <strong class="font-semibold">confirmada</strong>.
                </p>
            </div>

            <div
                class="flex items-center gap-2.5 bg-surface-container rounded-lg px-3 py-2.5
                        border border-outline-variant/20">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                    class="text-on-surface-variant flex-shrink-0">
                    <rect x="1.5" y="1.5" width="11" height="11" rx="2" stroke="currentColor"
                        stroke-width="1.3" />
                    <path d="M4 7h6M4 9.5h4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                </svg>
                <p class="text-xs text-on-surface-variant leading-snug">
                    El estado de la cita pasará de <em>Pendiente</em> a <em>Confirmada</em>.
                </p>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-outline-variant/20">
            <button wire:click="confirmAppointment" wire:loading.attr="disabled" wire:target="confirmAppointment"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold
                       bg-[#E6F5F0] text-[#0F6E56] border border-[#8ECFBB]
                       hover:bg-[#1D9E75] hover:text-white hover:border-[#1D9E75]
                       disabled:opacity-50 transition-colors duration-150">
                <span wire:loading.remove wire:target="confirmAppointment" class="inline-flex items-center gap-1.5">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                        <path d="M1.5 7l3 3 7-6.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Sí, confirmar
                </span>
                <span wire:loading wire:target="confirmAppointment" class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                        <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.5"
                            stroke-dasharray="20" stroke-dashoffset="10" stroke-linecap="round" />
                    </svg>
                    Guardando…
                </span>
            </button>

            <button wire:click="closeConfirmModal" wire:loading.attr="disabled" wire:target="confirmAppointment"
                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold
                       bg-surface-container border border-outline-variant/20 text-on-surface-variant
                       hover:bg-surface-container-high transition-colors duration-150">
                Cancelar
            </button>
        </div>

    </div>
</div>
