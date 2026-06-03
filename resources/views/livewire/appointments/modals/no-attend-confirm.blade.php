{{-- resources/views/livewire/appointments/modals/no-show-confirm.blade.php --}}
<div wire:click.self="closeNoAttendModal"
    class="fixed inset-0 z-[70] flex items-center justify-center
           bg-black/40 backdrop-blur-[2px] p-4">
    <div role="dialog" aria-modal="true" aria-labelledby="modal-title-noshow"
        class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30
               shadow-[0_8px_32px_rgba(0,0,0,0.12)] w-full max-w-sm flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/20">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-xl bg-[#FEF3E2] text-[#B45309]
                            flex items-center justify-center border border-[#F5C97A]">
                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <circle cx="7" cy="4" r="2.2" stroke="currentColor" stroke-width="1.3" />
                        <path d="M3 12c0-2.2 1.8-4 4-4s4 1.8 4 4" stroke="currentColor" stroke-width="1.3"
                            stroke-linecap="round" />
                        <line x1="1.5" y1="1.5" x2="12.5" y2="12.5" stroke="currentColor"
                            stroke-width="1.3" stroke-linecap="round" />
                    </svg>
                </div>
                <h2 id="modal-title-noshow" class="text-[15px] font-semibold text-on-surface">
                    Marcar inasistencia
                </h2>
            </div>
            <button wire:click="closeNoAttendModal" aria-label="Cerrar"
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
            <div class="flex items-start gap-3 bg-[#FEF3E2] border border-[#F5C97A] rounded-xl px-4 py-3">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none"
                    class="flex-shrink-0 mt-0.5 text-[#B45309]">
                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                    <path d="M8 5v3.5M8 10.5v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                <p class="text-[13px] text-[#B45309] leading-snug">
                    ¿Confirmas que el cliente <strong class="font-semibold">no asistió</strong> a esta cita?
                    Esta acción es irreversible y quedará registrada en el historial.
                </p>
            </div>

            <div
                class="flex items-center gap-2.5 bg-surface-container rounded-xl px-3 py-2.5
                        border border-outline-variant/20">
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none"
                    class="text-on-surface-variant flex-shrink-0">
                    <path d="M7 1.5A5.5 5.5 0 1 1 7 12.5 5.5 5.5 0 0 1 7 1.5Z" stroke="currentColor"
                        stroke-width="1.3" />
                    <path d="M5 7l1.5 1.5L9 5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <p class="text-[11.5px] text-on-surface-variant leading-snug">
                    La inasistencia se registrará en el dashboard y en el historial del cliente.
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-outline-variant/20">
            <button wire:click="noAttendAppointment" wire:loading.attr="disabled" wire:target="noAttendAppointment"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-semibold
                       bg-[#FEF3E2] text-[#B45309] border border-[#F5C97A]
                       hover:bg-[#D97706] hover:text-white hover:border-[#D97706]
                       disabled:opacity-50 transition-colors duration-150">
                <span wire:loading.remove wire:target="markNoShow" class="inline-flex items-center gap-1.5">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                        <path d="M2 2l9 9M11 2L2 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    Sí, marcar inasistencia
                </span>
                <span wire:loading wire:target="markNoShow" class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                        <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.5"
                            stroke-dasharray="20" stroke-dashoffset="10" stroke-linecap="round" />
                    </svg>
                    Guardando…
                </span>
            </button>
            <button wire:click="closeNoAttendModal" wire:loading.attr="disabled" wire:target="markNoShow"
                class="inline-flex items-center px-4 py-2 rounded-xl text-[13px] font-semibold
                       bg-surface-container border border-outline-variant/20 text-on-surface-variant
                       hover:bg-surface-container-high transition-colors duration-150">
                Cancelar
            </button>
        </div>
    </div>
</div>
