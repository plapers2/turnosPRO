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
        class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30
               shadow-[0_8px_32px_rgba(0,0,0,0.12)] w-full max-w-lg flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/20">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-xl bg-primary-fixed/20 text-primary
                            flex items-center justify-center border border-primary-fixed-dim/30">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                </div>
                <h2 id="modal-title-detail" class="text-[15px] font-semibold text-on-surface">
                    Detalle de cita
                </h2>
            </div>
            <button wire:click="closeModal" aria-label="Cerrar"
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
        <div class="p-5 flex flex-col gap-4 overflow-y-auto">
            <div class="grid grid-cols-2 gap-4">

                {{-- Cliente --}}
                <div class="flex flex-col gap-2">
                    <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">Cliente</p>
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 rounded-full bg-primary-fixed/30 text-primary
                                    flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($appt->customer?->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-[13px] font-semibold text-on-surface leading-tight">
                                {{ $appt->customer?->name }}
                            </p>
                            <p class="text-[11px] text-on-surface-variant mt-0.5">{{ $appt->customer?->email }}</p>
                            <p class="text-[11px] text-on-surface-variant">{{ $appt->customer?->phone }}</p>
                        </div>
                    </div>
                </div>

                {{-- Profesional --}}
                <div class="flex flex-col gap-2">
                    <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">Profesional
                    </p>
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 rounded-full bg-[#E1F5EE] text-[#0F6E56]
                                    flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($appt->user?->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-[13px] font-semibold text-on-surface leading-tight">{{ $appt->user?->name }}
                            </p>
                            <p class="text-[11px] text-on-surface-variant mt-0.5">{{ $appt->user?->email }}</p>
                        </div>
                    </div>
                </div>

                {{-- Fecha --}}
                <div class="flex flex-col gap-1.5">
                    <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">Fecha y hora
                    </p>
                    <p class="text-[13px] font-semibold text-on-surface">{{ $appt->start_time->format('d/m/Y') }}</p>
                    <p class="text-[11px] text-on-surface-variant">
                        {{ $appt->start_time->format('H:i') }} – {{ $appt->end_time->format('H:i') }}
                        ({{ $appt->start_time->diffInMinutes($appt->end_time) }} min)
                    </p>
                </div>

                {{-- Estado --}}
                <div class="flex flex-col gap-1.5">
                    <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">Estado</p>
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
                                 rounded-full text-[10.5px] font-semibold {{ $badgeClass }}">
                        {{ __('appt.status.' . $appt->status) }}
                    </span>
                </div>

                {{-- Servicios --}}
                <div class="col-span-2 flex flex-col gap-2">
                    <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">Servicios
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($appt->services as $svc)
                            <span
                                class="bg-surface-container border border-outline-variant/30
                                         text-on-surface-variant text-[11.5px] font-medium px-2.5 py-1 rounded-lg">
                                {{ $svc->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Auditoria --}}
                @if ($appt->confirmed_by || $appt->cancelled_by)
                    <div class="col-span-2 grid grid-cols-2 gap-4">
                        @if ($appt->confirmed_by)
                            <div class="flex flex-col gap-1.5">
                                <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">
                                    Confirmado por</p>
                                <span
                                    class="bg-[#E1F5EE] text-[#0F6E56] inline-flex items-center self-start
                                             px-2.5 py-1 rounded-full text-[10.5px] font-semibold">
                                    {{ $appt->approvedBy->name }}
                                </span>
                            </div>
                        @endif
                        @if ($appt->cancelled_by)
                            <div class="flex flex-col gap-1.5">
                                <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">
                                    Cancelado por</p>
                                <span
                                    class="bg-[#FCEBEB] text-[#A32D2D] inline-flex items-center self-start
                                             px-2.5 py-1 rounded-full text-[10.5px] font-semibold">
                                    {{ $appt->cancelledBy->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($appt->completed_by)
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">
                            Completado por</p>
                        <span
                            class="bg-[#E6F1FB] text-[#185FA5] inline-flex items-center self-start
                                     px-2.5 py-1 rounded-full text-[10.5px] font-semibold">
                            {{ $appt->completedBy?->name }}
                        </span>
                    </div>
                @endif

                @if ($appt->notes)
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">Notas
                        </p>
                        <p
                            class="text-[13px] text-on-surface bg-surface-container
                                  rounded-xl px-3 py-2.5 border border-outline-variant/20">
                            {{ $appt->notes }}
                        </p>
                    </div>
                @endif

                @if ($appt->cancellation_reason)
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">Motivo
                            de cancelación</p>
                        <p
                            class="text-[13px] text-[#A32D2D] bg-[#FCEBEB]
                                  rounded-xl px-3 py-2.5 border border-[#F7C1C1]">
                            {{ $appt->cancellation_reason }}
                        </p>
                    </div>
                @endif

                @if ($appt->status === 'completed' && $appt->completed_at)
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">
                            Completada el</p>
                        <p
                            class="text-[13px] text-[#185FA5] bg-[#E6F1FB]
                                  rounded-xl px-3 py-2.5 border border-[#9EC8F0]">
                            {{ $appt->completed_at }}
                        </p>
                    </div>
                @endif
                {{-- Historial de transiciones --}}
                @if ($appt->statusLogs->isNotEmpty())
                    <div class="col-span-2 flex flex-col gap-2">
                        <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">
                            Historial de cambios
                        </p>
                        <div class="flex flex-col gap-1.5">
                            @foreach ($appt->statusLogs as $log)
                                @php
                                    $logColor = match ($log->to_status) {
                                        'confirmed' => 'bg-[#E1F5EE] text-[#0F6E56] border-[#9FE1CB]',
                                        'cancelled' => 'bg-[#FCEBEB] text-[#A32D2D] border-[#F7C1C1]',
                                        'completed' => 'bg-[#E6F1FB] text-[#185FA5] border-[#9EC8F0]',
                                        default => 'bg-[#FAEEDA] text-[#854F0B] border-[#F0D4A0]',
                                    };
                                @endphp
                                <div
                                    class="flex items-start justify-between gap-2 px-3 py-2 rounded-xl border
                            bg-surface-container border-outline-variant/20 text-[11.5px]">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if ($log->from_status)
                                            <span class="font-medium text-on-surface-variant">
                                                {{ __('appt.status.' . $log->from_status) }}
                                            </span>
                                            <span class="text-on-surface-variant">→</span>
                                        @endif
                                        <span
                                            class="px-2 py-0.5 rounded-full font-semibold border {{ $logColor }}">
                                            {{ __('appt.status.' . $log->to_status) }}
                                        </span>
                                        @if ($log->changedBy)
                                            <span class="text-on-surface-variant">por
                                                <strong>{{ $log->changedBy->name }}</strong></span>
                                        @else
                                            <span class="text-on-surface-variant italic">Sistema automático</span>
                                        @endif
                                    </div>
                                    <span class="text-on-surface-variant whitespace-nowrap flex-shrink-0">
                                        {{ $log->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-outline-variant/20">


            @if ($appt->status === 'confirmed' && now()->gte($appt->end_time))
                <button wire:click="openCompleteAndClose({{ $appt->id }})" wire:loading.attr="disabled"
                    wire:target="openCompleteAndClose({{ $appt->id }})"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-semibold
                           bg-[#E6F1FB] text-[#185FA5] border border-[#9EC8F0]
                           hover:bg-[#378ADD] hover:text-white hover:border-[#378ADD]
                           disabled:opacity-50 transition-colors duration-150">
                    <span wire:loading.remove wire:target="openCompleteAndClose({{ $appt->id }})"
                        class="inline-flex items-center gap-1.5">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                            <path d="M1.5 7l3 3 7-6.5" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
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

            @role('admin')
                @if (in_array($appt->status, ['confirmed']))
                    <button wire:click="openCancelAndClose({{ $appt->id }})" wire:loading.attr="disabled"
                        wire:target="openCancelAndClose({{ $appt->id }})"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-semibold
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
            @endrole

            <button wire:click="closeModal"
                class="inline-flex items-center px-4 py-2 rounded-xl text-[13px] font-semibold
                       bg-surface-container border border-outline-variant/20 text-on-surface-variant
                       hover:bg-surface-container-high transition-colors duration-150">
                Cerrar
            </button>
            {{-- Comprobante PDF --}}
            <a href="{{ route('appointment.voucher', $appt->id) }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-semibold
           bg-surface-container border border-outline-variant/20 text-on-surface-variant
           hover:bg-surface-container-high transition-colors duration-150">
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                    <path d="M4 1h6l3 3v9H1V1h3z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                    <path d="M4 8h6M4 10.5h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                Comprobante
            </a>
        </div>
    </div>
</div>
