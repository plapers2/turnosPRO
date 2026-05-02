{{-- resources/views/livewire/appointments/modals/appointment-detail.blade.php --}}
<div x-data="{ show: false }" x-init="$nextTick(() => show = true)" x-show="show" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" @close-modal.window="show = false" wire:click.self="closeModal"
    class="fixed inset-0 z-[70] flex items-center justify-center
           bg-black/40 backdrop-blur-[2px] p-4">
    <div x-show="show" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2" role="dialog" aria-modal="true"
        aria-labelledby="modal-title-detail"
        class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
               shadow-lg w-full max-w-lg flex flex-col overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4
                    border-b border-outline-variant/20">
            <h2 id="modal-title-detail" class="text-base font-semibold text-primary">
                Detalle de cita
            </h2>
            <button wire:click="closeModal" aria-label="Cerrar"
                class="w-7 h-7 flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 flex flex-col gap-4 overflow-y-auto">
            <div class="grid grid-cols-2 gap-4">

                {{-- Cliente --}}
                <div class="flex flex-col gap-2">
                    <p class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                        Cliente
                    </p>
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 rounded-full bg-[#E6F1FB] text-[#185FA5]
                                    flex items-center justify-center text-xs font-semibold flex-shrink-0">
                            {{ strtoupper(substr($appt->customer->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-on-surface leading-tight">
                                {{ $appt->customer->name }}
                            </p>
                            <p class="text-xs text-on-surface-variant mt-0.5">
                                {{ $appt->customer->email }}
                            </p>
                            <p class="text-xs text-on-surface-variant">
                                {{ $appt->customer->phone }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Profesional --}}
                <div class="flex flex-col gap-2">
                    <p class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                        Profesional
                    </p>
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 rounded-full bg-[#E1F5EE] text-[#0F6E56]
                                    flex items-center justify-center text-xs font-semibold flex-shrink-0">
                            {{ strtoupper(substr($appt->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-on-surface leading-tight">
                                {{ $appt->user->name }}
                            </p>
                            <p class="text-xs text-on-surface-variant mt-0.5">
                                {{ $appt->user->email }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Fecha y hora --}}
                <div class="flex flex-col gap-1.5">
                    <p class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                        Fecha y hora
                    </p>
                    <p class="text-sm font-semibold text-on-surface">
                        {{ $appt->start_time->format('d/m/Y') }}
                    </p>
                    <p class="text-xs text-on-surface-variant">
                        {{ $appt->start_time->format('H:i') }} – {{ $appt->end_time->format('H:i') }}
                        ({{ $appt->start_time->diffInMinutes($appt->end_time) }} min)
                    </p>
                </div>

                {{-- Estado --}}
                <div class="flex flex-col gap-1.5">
                    <p class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                        Estado
                    </p>
                    @php
                        $badgeClass = match ($appt->status) {
                            'pending' => 'bg-[#FAEEDA] text-[#854F0B]',
                            'confirmed' => 'bg-[#E1F5EE] text-[#0F6E56]',
                            'cancelled' => 'bg-[#FCEBEB] text-[#A32D2D]',
                            'completed' => 'bg-[#E6F1FB] text-[#185FA5]',
                            default => 'bg-surface-container text-on-surface-variant',
                        };
                    @endphp
                    <span
                        class="inline-flex items-center self-start px-2.5 py-1
                                 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                        {{ __('appt.status.' . $appt->status) }}
                    </span>
                </div>

                {{-- Servicios --}}
                <div class="col-span-2 flex flex-col gap-2">
                    <p class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                        Servicios
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($appt->services as $svc)
                            <span
                                class="bg-surface-container border border-outline-variant/30
                                         text-on-surface-variant text-xs font-medium
                                         px-2.5 py-1 rounded-md">
                                {{ $svc->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Notas --}}
                @if ($appt->notes)
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <p class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                            Notas
                        </p>
                        <p
                            class="text-sm text-on-surface bg-surface-container
                                  rounded-lg px-3 py-2.5 border border-outline-variant/20">
                            {{ $appt->notes }}
                        </p>
                    </div>
                @endif

                {{-- Motivo de cancelación --}}
                @if ($appt->cancellation_reason)
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <p class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                            Motivo de cancelación
                        </p>
                        <p
                            class="text-sm text-[#A32D2D] bg-[#FCEBEB]
                                  rounded-lg px-3 py-2.5 border border-[#F7C1C1]">
                            {{ $appt->cancellation_reason }}
                        </p>
                    </div>
                @endif

                {{-- RF-26: Fecha de completado --}}
                @if ($appt->status === 'completed' && $appt->completed_at)
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <p class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                            Completada el
                        </p>
                        <p
                            class="text-sm text-[#185FA5] bg-[#E6F1FB]
                                  rounded-lg px-3 py-2.5 border border-[#9EC8F0]">
                            {{ $appt->completed_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @endif

            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-2 px-5 py-4
            border-t border-outline-variant/20">

            {{-- Confirmar (solo pendientes) --}}
            @if ($appt->status === 'pending')
                <button wire:click="openConfirmAndClose({{ $appt->id }})" wire:loading.attr="disabled"
                    wire:target="openConfirmAndClose({{ $appt->id }})"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold
                   bg-[#E1F5EE] text-[#0F6E56] border border-[#9FE1CB]
                   hover:bg-[#1D9E75] hover:text-white hover:border-[#1D9E75]
                   disabled:opacity-50 transition-colors duration-150">
                    <span wire:loading.remove wire:target="openConfirmAndClose({{ $appt->id }})">Confirmar
                        cita</span>
                    <span wire:loading wire:target="openConfirmAndClose({{ $appt->id }})"
                        class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                            <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.5"
                                stroke-dasharray="20" stroke-dashoffset="10" stroke-linecap="round" />
                        </svg>
                        Cargando…
                    </span>
                </button>
            @endif

            {{-- RF-26: Completar (confirmada + end_time pasado) --}}
            @if ($appt->status === 'confirmed' && now()->gte($appt->end_time))
                <button wire:click="openCompleteAndClose({{ $appt->id }})" wire:loading.attr="disabled"
                    wire:target="openCompleteAndClose({{ $appt->id }})"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold
                   bg-[#E6F1FB] text-[#185FA5] border border-[#9EC8F0]
                   hover:bg-[#378ADD] hover:text-white hover:border-[#378ADD]
                   disabled:opacity-50 transition-colors duration-150">
                    <span wire:loading.remove wire:target="openCompleteAndClose({{ $appt->id }})"
                        class="inline-flex items-center gap-1.5">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                            <path d="M1.5 7l3 3 7-6.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        Completar cita
                    </span>
                    <span wire:loading wire:target="openCompleteAndClose({{ $appt->id }})"
                        class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                            <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.5"
                                stroke-dasharray="20" stroke-dashoffset="10" stroke-linecap="round" />
                        </svg>
                        Procesando…
                    </span>
                </button>
            @endif

            {{-- Cancelar (pendientes y confirmadas) --}}
            @if (in_array($appt->status, ['pending', 'confirmed']))
                <button wire:click="openCancelAndClose({{ $appt->id }})" wire:loading.attr="disabled"
                    wire:target="openCancelAndClose({{ $appt->id }})"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold
                   bg-[#FCEBEB] text-[#A32D2D] border border-[#F7C1C1]
                   hover:bg-[#E24B4A] hover:text-white hover:border-[#E24B4A]
                   disabled:opacity-50 transition-colors duration-150">
                    <span wire:loading.remove wire:target="openCancelAndClose({{ $appt->id }})">Cancelar
                        cita</span>
                    <span wire:loading wire:target="openCancelAndClose({{ $appt->id }})"
                        class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                            <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.5"
                                stroke-dasharray="20" stroke-dashoffset="10" stroke-linecap="round" />
                        </svg>
                        Procesando…
                    </span>
                </button>
            @endif

            <button wire:click="closeModal"
                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold
               bg-surface-container border border-outline-variant/20 text-on-surface-variant
               hover:bg-surface-container-high transition-colors duration-150">
                Cerrar
            </button>

        </div>
    </div>
</div>
