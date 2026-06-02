{{-- resources/views/livewire/appointments/modals/delay-confirm.blade.php --}}
<div wire:click.self="closeDelayModal"
    class="fixed inset-0 z-[70] flex items-center justify-center
           bg-black/40 backdrop-blur-[2px] p-4">
    <div role="dialog" aria-modal="true" aria-labelledby="modal-title-delay"
        class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30
               shadow-[0_8px_32px_rgba(0,0,0,0.12)] w-full max-w-sm flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/20">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-[#FAEEDA] text-[#854F0B]
                            flex items-center justify-center border border-[#FAC775]">
                    <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                        <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M8 5v3.5l2 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <h2 id="modal-title-delay" class="text-[15px] font-semibold text-on-surface">
                    Notificar atraso
                </h2>
            </div>
            <button wire:click="closeDelayModal" aria-label="Cerrar"
                class="w-7 h-7 flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150">
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                    <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 flex flex-col gap-4">

            {{-- Banner de afectados --}}
            <div class="flex items-start gap-3 bg-[#FAEEDA] border border-[#FAC775]
                        rounded-xl px-4 py-3">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" class="flex-shrink-0 mt-0.5">
                    <path d="M8 2L14.5 13.5H1.5L8 2Z" stroke="#854F0B" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M8 6.5v3M8 11.5v.5" stroke="#854F0B" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <p class="text-[13px] text-[#854F0B] leading-snug">
                    Se notificará a
                    <strong>{{ $delayRecipients }} cliente(s)</strong>
                    con citas confirmadas hoy.
                </p>
            </div>

            {{-- Input minutos --}}
            <div class="flex flex-col gap-1.5">
                <label for="delay-minutes" class="text-[11.5px] font-semibold text-on-surface-variant">
                    Tiempo de atraso (minutos)
                </label>
                <input id="delay-minutes"
                       type="number"
                       wire:model="delayMinutes"
                       min="1" max="480"
                       class="w-32 bg-surface-container border rounded-xl px-3 py-2
                              text-[13px] text-on-surface text-center
                              outline-none transition-all duration-150
                              {{ $errors->has('delayMinutes')
                                  ? 'border-error/60 focus:ring-2 focus:ring-error/20'
                                  : 'border-outline-variant/30 focus:ring-2 focus:ring-primary/20' }}" />
                @error('delayMinutes')
                    <p class="text-[11.5px] text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-outline-variant/20">
            <button wire:click="sendDelayNotification"
                    wire:loading.attr="disabled"
                    wire:target="sendDelayNotification"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-semibold
                       bg-[#FAEEDA] text-[#854F0B] border border-[#FAC775]
                       hover:bg-[#FAC775] disabled:opacity-50 transition-colors duration-150">
                <span wire:loading.remove wire:target="sendDelayNotification" class="inline-flex items-center gap-1.5">
                    <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                        <path d="M1 7l4 4 8-8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Confirmar envío
                </span>
                <span wire:loading wire:target="sendDelayNotification" class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                        <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.5"
                            stroke-dasharray="20" stroke-dashoffset="10" stroke-linecap="round"/>
                    </svg>
                    Enviando…
                </span>
            </button>
            <button wire:click="closeDelayModal"
                class="inline-flex items-center px-4 py-2 rounded-xl text-[13px] font-semibold
                       bg-surface-container border border-outline-variant/20 text-on-surface-variant
                       hover:bg-surface-container-high transition-colors duration-150">
                Cancelar
            </button>
        </div>
    </div>
</div>
