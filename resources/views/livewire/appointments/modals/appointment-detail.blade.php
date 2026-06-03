{{-- resources/views/livewire/appointments/modals/appointment-detail.blade.php --}}
<div x-data="{ show: false }" x-init="$nextTick(() => show = true)" x-show="show" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" @close-modal.window="show = false" wire:click.self="closeModal"
    class="fixed inset-0 z-[70] flex items-center justify-center bg-black/40 backdrop-blur-[2px] p-4">

    <div x-show="show" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2" role="dialog" aria-modal="true"
        aria-labelledby="modal-title-detail"
        class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30
               shadow-[0_8px_32px_rgba(0,0,0,0.12)] w-full max-w-lg flex flex-col overflow-hidden max-h-[90vh]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/20 flex-shrink-0">
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
                       text-on-surface-variant hover:bg-surface-container-high transition-colors duration-150">
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                    <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 flex flex-col gap-4 overflow-y-auto flex-1">
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

                {{-- Auditoría --}}
                @if ($appt->confirmed_by || $appt->cancelled_by || $appt->completed_by)
                    <div class="col-span-2 grid grid-cols-3 gap-2">
                        @if ($appt->confirmed_by)
                            <div class="flex flex-col gap-1 p-2.5 rounded-xl bg-[#E1F5EE] border border-[#9FE1CB]">
                                <p class="text-[9.5px] font-semibold text-[#0F6E56] uppercase tracking-wider">Confirmado
                                    por</p>
                                <p class="text-[12px] font-semibold text-[#0F6E56] truncate">
                                    {{ $appt->approvedBy->name }}</p>
                            </div>
                        @endif
                        @if ($appt->cancelled_by)
                            <div class="flex flex-col gap-1 p-2.5 rounded-xl bg-[#FCEBEB] border border-[#F7C1C1]">
                                <p class="text-[9.5px] font-semibold text-[#A32D2D] uppercase tracking-wider">Cancelado
                                    por</p>
                                <p class="text-[12px] font-semibold text-[#A32D2D] truncate">
                                    {{ $appt->cancelledBy->name }}</p>
                            </div>
                        @endif
                        @if ($appt->completed_by)
                            <div class="flex flex-col gap-1 p-2.5 rounded-xl bg-[#E6F1FB] border border-[#9EC8F0]">
                                <p class="text-[9.5px] font-semibold text-[#185FA5] uppercase tracking-wider">Completado
                                    por</p>
                                <p class="text-[12px] font-semibold text-[#185FA5] truncate">
                                    {{ $appt->completedBy?->name }}</p>
                            </div>
                        @endif
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
                            class="text-[13px] text-[#A32D2D] bg-[#FCEBEB] rounded-xl px-3 py-2.5 border border-[#F7C1C1]">
                            {{ $appt->cancellation_reason }}
                        </p>
                    </div>
                @endif

                @if ($appt->status === 'completed' && $appt->completed_at)
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">
                            Completada el</p>
                        <p
                            class="text-[13px] text-[#185FA5] bg-[#E6F1FB] rounded-xl px-3 py-2.5 border border-[#9EC8F0]">
                            {{ $appt->completed_at }}
                        </p>
                    </div>
                @endif

                {{-- Historial de cambios — tabla compacta horizontal --}}
                @if ($appt->statusLogs->isNotEmpty())
                    <div class="col-span-2 flex flex-col gap-2">
                        <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">
                            Historial de cambios
                        </p>
                        <div class="rounded-xl border border-outline-variant/20 overflow-hidden">
                            <table class="w-full text-[11px]">
                                <thead>
                                    <tr class="bg-surface-container border-b border-outline-variant/20">
                                        <th
                                            class="text-left px-3 py-2 text-[10px] font-semibold text-on-surface-variant uppercase tracking-wider">
                                            Evento</th>
                                        <th
                                            class="text-left px-3 py-2 text-[10px] font-semibold text-on-surface-variant uppercase tracking-wider">
                                            Por</th>
                                        <th
                                            class="text-right px-3 py-2 text-[10px] font-semibold text-on-surface-variant uppercase tracking-wider">
                                            Fecha</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/10">
                                    @foreach ($appt->statusLogs as $log)
                                        @php
                                            $isReassign = $log->from_status === 'reassigned';

                                            $chipClass = match (true) {
                                                $isReassign => 'bg-[#F0EAFA] text-[#6B3FA0] border-[#C9A8F0]',
                                                $log->to_status === 'confirmed'
                                                    => 'bg-[#E1F5EE] text-[#0F6E56] border-[#9FE1CB]',
                                                $log->to_status === 'cancelled'
                                                    => 'bg-[#FCEBEB] text-[#A32D2D] border-[#F7C1C1]',
                                                $log->to_status === 'completed'
                                                    => 'bg-[#E6F1FB] text-[#185FA5] border-[#9EC8F0]',
                                                default => 'bg-[#FAEEDA] text-[#854F0B] border-[#F0D4A0]',
                                            };

                                            $icon = match (true) {
                                                $isReassign => 'swap_horiz',
                                                $log->to_status === 'confirmed' => 'check_circle',
                                                $log->to_status === 'cancelled' => 'cancel',
                                                $log->to_status === 'completed' => 'task_alt',
                                                default => 'schedule',
                                            };

                                            $label = $isReassign
                                                ? 'Profesional cambiado'
                                                : __('appt.status.' . $log->to_status);
                                        @endphp
                                        <tr class="hover:bg-surface-container/40 transition-colors">
                                            <td class="px-3 py-2">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full border font-semibold {{ $chipClass }}">
                                                    <span
                                                        class="material-symbols-outlined text-[11px]">{{ $icon }}</span>
                                                    {{ $label }}
                                                </span>
                                                @if ($log->reason)
                                                    <p class="text-[10px] text-on-surface-variant mt-0.5 truncate max-w-[160px]"
                                                        title="{{ $log->reason }}">
                                                        {{ $log->reason }}
                                                    </p>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-on-surface-variant">
                                                @if ($log->changedBy)
                                                    <span
                                                        class="font-medium text-on-surface">{{ $log->changedBy->name }}</span>
                                                @else
                                                    <span class="italic">Sistema</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-on-surface-variant text-right whitespace-nowrap">
                                                {{ $log->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Panel de reasignación --}}
                @role('admin')
                    @if (in_array($appt->status, ['confirmed', 'pending']) && $reasignarAppointmentId === $appt->id)
                        <div
                            class="col-span-2 flex flex-col gap-3 p-4 rounded-xl bg-surface-container border border-outline-variant/30">
                            <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-wider">
                                Cambiar profesional
                            </p>

                            @if ($loadingProfesionales)
                                <div class="flex items-center gap-2 py-3">
                                    <svg class="animate-spin w-4 h-4 text-primary" viewBox="0 0 14 14" fill="none">
                                        <circle cx="7" cy="7" r="5.5" stroke="currentColor"
                                            stroke-width="1.5" stroke-dasharray="20" stroke-dashoffset="10"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span class="text-[12px] text-on-surface-variant">Buscando disponibilidad...</span>
                                </div>
                            @elseif (empty($profesionalesDisponibles))
                                <div class="flex items-center gap-2 py-2 text-[12px] text-[#A32D2D]">
                                    <span class="material-symbols-outlined text-[16px]">event_busy</span>
                                    No hay profesionales disponibles para este horario y servicio.
                                </div>
                            @else
                                <div class="flex flex-col gap-2">
                                    @foreach ($profesionalesDisponibles as $prof)
                                        @php $esActual = $appt->user_id == $prof['id']; @endphp
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-xl border-2 transition-all
                                                       {{ (int) $nuevoProfesionalId === (int) $prof['id'] ? 'border-primary bg-primary/5' : 'border-outline-variant/20 hover:border-primary/40' }}
                                                       {{ $esActual ? 'opacity-50 pointer-events-none' : 'cursor-pointer' }}">
                                            <input type="radio" wire:model.live="nuevoProfesionalId"
                                                value="{{ $prof['id'] }}" class="sr-only"
                                                @disabled($esActual) />
                                            <div
                                                class="w-9 h-9 rounded-full flex-shrink-0 bg-primary/10
                                                       flex items-center justify-center border border-outline-variant/20 overflow-hidden">
                                                @if (!empty($prof['image']))
                                                    <img src="/storage/{{ $prof['image'] }}"
                                                        class="w-full h-full object-cover" />
                                                @else
                                                    <span class="text-xs font-bold text-primary/60">
                                                        {{ strtoupper(substr($prof['name'], 0, 2)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[13px] font-semibold text-on-surface">
                                                    {{ $prof['name'] }}
                                                    @if ($esActual)
                                                        <span
                                                            class="text-[10px] text-on-surface-variant font-normal ml-1">(actual)</span>
                                                    @endif
                                                </p>
                                                <p class="text-[11px] text-on-surface-variant">{{ $prof['email'] }}</p>
                                            </div>
                                            @if ((int) $nuevoProfesionalId === (int) $prof['id'])
                                                <span
                                                    class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>

                                <textarea wire:model="reasignarRazon" rows="2" placeholder="Motivo del cambio (opcional)..."
                                    class="w-full px-3 py-2 rounded-lg bg-surface-container border border-outline-variant/30
                                           text-[12px] text-on-surface placeholder:text-on-surface-variant/50
                                           focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary
                                           transition resize-none"></textarea>

                                @error('nuevoProfesionalId')
                                    <p class="text-[11px] text-[#A32D2D]">{{ $message }}</p>
                                @enderror
                            @endif

                            <div class="flex gap-2 mt-1">
                                @unless (empty($profesionalesDisponibles) || $loadingProfesionales)
                                    <button wire:click="confirmarReasignacion" wire:loading.attr="disabled"
                                        wire:target="confirmarReasignacion" @disabled(!$nuevoProfesionalId || (int) $nuevoProfesionalId === (int) $appt->user_id)
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold
                                               bg-primary text-white hover:bg-primary/90 disabled:opacity-40
                                               disabled:cursor-not-allowed transition-colors">
                                        <span wire:loading.remove wire:target="confirmarReasignacion">Confirmar cambio</span>
                                        <span wire:loading wire:target="confirmarReasignacion"
                                            class="inline-flex items-center gap-1.5">
                                            <svg class="animate-spin w-3 h-3" viewBox="0 0 14 14" fill="none">
                                                <circle cx="7" cy="7" r="5.5" stroke="currentColor"
                                                    stroke-width="1.5" stroke-dasharray="20" stroke-dashoffset="10"
                                                    stroke-linecap="round" />
                                            </svg>
                                            Guardando...
                                        </span>
                                    </button>
                                @endunless
                                <button wire:click="cancelarReasignacion"
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-[12px] font-semibold
                                           bg-surface-container border border-outline-variant/20 text-on-surface-variant
                                           hover:bg-surface-container-high transition-colors">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    @endif
                @endrole

            </div>
        </div>

        {{-- Footer — botones reorganizados --}}
        <div
            class="flex items-center justify-between gap-2 px-5 py-4 border-t border-outline-variant/20 flex-shrink-0">

            {{-- Izquierda: acciones destructivas / secundarias --}}
            <div class="flex items-center gap-2">
                @role('admin')
                    @if (in_array($appt->status, ['confirmed']) && $reasignarAppointmentId !== $appt->id)
                        <button wire:click="openCancelAndClose({{ $appt->id }})" wire:loading.attr="disabled"
                            wire:target="openCancelAndClose({{ $appt->id }})"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-[12px] font-semibold
                                   bg-[#FCEBEB] text-[#A32D2D] border border-[#F7C1C1]
                                   hover:bg-[#E24B4A] hover:text-white hover:border-[#E24B4A]
                                   disabled:opacity-50 transition-colors duration-150">
                            <span wire:loading.remove wire:target="openCancelAndClose({{ $appt->id }})">
                                <span class="material-symbols-outlined text-[14px] align-middle">event_busy</span>
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
                @endrole
            </div>

            {{-- Derecha: acciones positivas + navegación --}}
            <div class="flex items-center gap-2">

                @if ($appt->status === 'confirmed' && now()->gte($appt->end_time))
                    <button wire:click="openCompleteAndClose({{ $appt->id }})" wire:loading.attr="disabled"
                        wire:target="openCompleteAndClose({{ $appt->id }})"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-[12px] font-semibold
                               bg-[#E6F1FB] text-[#185FA5] border border-[#9EC8F0]
                               hover:bg-[#378ADD] hover:text-white hover:border-[#378ADD]
                               disabled:opacity-50 transition-colors duration-150">
                        <span wire:loading.remove wire:target="openCompleteAndClose({{ $appt->id }})"
                            class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px]">task_alt</span>
                            Completar
                        </span>
                        <span wire:loading wire:target="openCompleteAndClose({{ $appt->id }})"
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

                @role('admin')
                    @if (in_array($appt->status, ['confirmed', 'pending']) && $reasignarAppointmentId !== $appt->id)
                        <button wire:click="cargarProfesionalesReasignar({{ $appt->id }})"
                            wire:loading.attr="disabled" wire:target="cargarProfesionalesReasignar({{ $appt->id }})"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-[12px] font-semibold
                                   bg-surface-container border border-outline-variant/20 text-on-surface-variant
                                   hover:bg-surface-container-high transition-colors duration-150">
                            <span wire:loading.remove wire:target="cargarProfesionalesReasignar({{ $appt->id }})">
                                <span class="material-symbols-outlined text-[14px] align-middle">swap_horiz</span>
                                Reasignar
                            </span>
                            <span wire:loading wire:target="cargarProfesionalesReasignar({{ $appt->id }})"
                                class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 14 14" fill="none">
                                    <circle cx="7" cy="7" r="5.5" stroke="currentColor"
                                        stroke-width="1.5" stroke-dasharray="20" stroke-dashoffset="10"
                                        stroke-linecap="round" />
                                </svg>
                                Cargando...
                            </span>
                        </button>
                    @endif
                @endrole

                <a href="{{ route('appointment.voucher', $appt->id) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-[12px] font-semibold
                           bg-surface-container border border-outline-variant/20 text-on-surface-variant
                           hover:bg-surface-container-high transition-colors duration-150">
                    <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                        <path d="M4 1h6l3 3v9H1V1h3z" stroke="currentColor" stroke-width="1.5"
                            stroke-linejoin="round" />
                        <path d="M4 8h6M4 10.5h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    PDF
                </a>

                <button wire:click="closeModal"
                    class="inline-flex items-center px-3.5 py-2 rounded-xl text-[12px] font-semibold
                           bg-surface-container border border-outline-variant/20 text-on-surface-variant
                           hover:bg-surface-container-high transition-colors duration-150">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
