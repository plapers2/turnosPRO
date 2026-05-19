<div x-data="{ show: false }" x-init="$nextTick(() => show = true)"
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    wire:click.self="closeCancelModal"
    class="fixed inset-0 z-[70] flex items-center justify-center
           bg-black/40 backdrop-blur-[2px] p-4">

    <div x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30
               shadow-[0_8px_32px_rgba(0,0,0,0.12)] w-full max-w-md flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/20">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-[#FCEBEB] text-[#A32D2D]
                            flex items-center justify-center border border-[#F7C1C1]">
                    <svg width="15" height="15" viewBox="0 0 14 14" fill="none">
                        <path d="M2 2l10 10M12 2L2 12" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </div>
                <h2 class="text-[15px] font-semibold text-on-surface">Cancelar cita</h2>
            </div>
            <button wire:click="closeCancelModal"
                class="w-7 h-7 flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150">
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                    <path d="M2 2l10 10M12 2L2 12" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 flex flex-col gap-4">
            <p class="text-[13px] text-on-surface-variant">
                ¿Estás seguro de que deseas cancelar esta cita? Esta acción no se puede deshacer.
            </p>
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider">
                    Motivo (opcional)
                </label>
                <textarea wire:model.live="cancelReason" rows="3"
                    placeholder="Escribe el motivo de la cancelación…"
                    class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/30
                           bg-surface-container text-[13px] text-on-surface
                           placeholder:text-on-surface-variant/50
                           focus:outline-none focus:ring-2 focus:ring-primary/30 resize-none">
                </textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-outline-variant/20">
            <button wire:click="closeCancelModal"
                class="px-4 py-2 rounded-xl text-[13px] font-semibold
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150">
                Volver
            </button>
            <button wire:click="confirmCancel" wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-semibold
                       bg-[#FCEBEB] text-[#A32D2D] border border-[#F7C1C1]
                       hover:bg-[#E24B4A] hover:text-white hover:border-[#E24B4A]
                       disabled:opacity-50 transition-colors duration-150">
                <span wire:loading.remove wire:target="confirmCancel">Confirmar cancelación</span>
                <span wire:loading wire:target="confirmCancel"
                    class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                        <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.5"
                            stroke-dasharray="20" stroke-dashoffset="10" stroke-linecap="round" />
                    </svg>
                    Cancelando…
                </span>
            </button>
        </div>
    </div>
</div>