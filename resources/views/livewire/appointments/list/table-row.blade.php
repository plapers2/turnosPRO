{{-- resources/views/livewire/appointments/list/table-row.blade.php --}}
@php
    $canComplete = $appt->status === 'confirmed' && now()->gte($appt->end_time);
    $pendingEnd = $appt->status === 'confirmed' && now()->lt($appt->end_time);

    $badgeClass = match ($appt->status) {
        'pending' => 'bg-[#FAEEDA] text-[#854F0B]',
        'confirmed' => 'bg-[#E1F5EE] text-[#0F6E56]',
        'cancelled' => 'bg-[#FCEBEB] text-[#A32D2D]',
        'completed' => 'bg-[#E6F1FB] text-[#185FA5]',
        default => 'bg-surface-container text-on-surface-variant',
    };
    $badgeLabel = match ($appt->status) {
        'pending' => 'Pendiente',
        'confirmed' => 'Confirmada',
        'cancelled' => 'Cancelada',
        'completed' => 'Completada',
        default => $appt->status,
    };
@endphp

<tr wire:key="row-{{ $appt->id }}"
    class="border-b border-outline-variant/20 last:border-b-0
           hover:bg-surface-container-low/60 transition-colors duration-100">

    {{-- Cliente --}}
    <td class="px-4 py-3">
        <div class="flex items-center gap-2.5">
            <div
                class="w-8 h-8 rounded-full bg-primary-fixed/30 text-primary
                        flex items-center justify-center text-[11px] font-bold flex-shrink-0">
                {{ strtoupper(substr($appt->customer->name, 0, 2)) }}
            </div>
            <div>
                <p class="text-[13px] font-semibold text-on-surface leading-tight">{{ $appt->customer->name }}</p>
                <p class="text-[11px] text-on-surface-variant mt-0.5">{{ $appt->customer->phone }}</p>
            </div>
        </div>
    </td>

    {{-- Profesional --}}
    <td class="px-4 py-3">
        <div class="flex items-center gap-2.5">
            <div
                class="w-8 h-8 rounded-full bg-[#E1F5EE] text-[#0F6E56]
                        flex items-center justify-center text-[11px] font-bold flex-shrink-0">
                {{ strtoupper(substr($appt->user->name, 0, 2)) }}
            </div>
            <span class="text-[13px] text-on-surface">{{ $appt->user->name }}</span>
        </div>
    </td>

    {{-- Servicios --}}
    <td class="px-4 py-3">
        <div class="flex flex-wrap gap-1">
            @foreach ($appt->services->take(2) as $svc)
                <span
                    class="inline-block bg-surface-container border border-outline-variant/30
                             text-on-surface-variant text-[10.5px] font-medium px-2 py-0.5 rounded-lg">
                    {{ $svc->name }}
                </span>
            @endforeach
            @if ($appt->services->count() > 2)
                <span
                    class="inline-block bg-surface-container border border-outline-variant/30
                             text-on-surface-variant text-[10.5px] font-medium px-2 py-0.5 rounded-lg">
                    +{{ $appt->services->count() - 2 }}
                </span>
            @endif
        </div>
    </td>

    {{-- Fecha --}}
    <td class="px-4 py-3">
        <p class="text-[13px] font-semibold text-on-surface leading-tight">
            {{ $appt->start_time->format('d/m/Y') }}
        </p>
        <p class="text-[11px] text-on-surface-variant mt-0.5">
            {{ $appt->start_time->format('H:i') }} – {{ $appt->end_time->format('H:i') }}
        </p>
    </td>

    {{-- Duración --}}
    <td class="px-4 py-3">
        <span class="text-[12px] text-on-surface-variant">
            {{ $appt->start_time->diffInMinutes($appt->end_time) }} min
        </span>
    </td>

    {{-- Estado --}}
    <td class="px-4 py-3">
        <span
            class="inline-flex items-center px-2.5 py-1 rounded-full text-[10.5px] font-semibold {{ $badgeClass }}">
            {{ $badgeLabel }}
        </span>
    </td>

    {{-- Acciones --}}
    <td class="px-4 py-3">
        <div class="flex items-center gap-1">

            {{-- Ver --}}
            <button wire:click="viewAppointment({{ $appt->id }})" title="Ver detalle"
                class="w-[30px] h-[30px] flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/30
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <circle cx="7" cy="7" r="2.5" stroke="currentColor" stroke-width="1.5" />
                    <path d="M1 7s2-4.5 6-4.5S13 7 13 7s-2 4.5-6 4.5S1 7 1 7z" stroke="currentColor"
                        stroke-width="1.5" />
                </svg>
            </button>

            {{-- Confirmar --}}
            @if ($appt->status === 'pending')
                <button wire:click="openConfirmModal({{ $appt->id }})" title="Confirmar cita"
                    class="w-[30px] h-[30px] flex items-center justify-center rounded-lg
                           bg-[#E1F5EE] border border-[#9FE1CB] text-[#0F6E56]
                           hover:bg-[#1D9E75] hover:text-white hover:border-[#1D9E75]
                           transition-colors duration-150">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M2 7l3.5 3.5L12 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            @endif

            {{-- Completar activo --}}
            @if ($canComplete)
                <button wire:click="openCompleteModal({{ $appt->id }})" title="Marcar como completada"
                    class="w-[30px] h-[30px] flex items-center justify-center rounded-lg
                           bg-[#E6F1FB] border border-[#9EC8F0] text-[#185FA5]
                           hover:bg-[#378ADD] hover:text-white hover:border-[#378ADD]
                           transition-colors duration-150">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M1.5 7.5l3 3 8-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <circle cx="7" cy="7" r="6" stroke="currentColor" stroke-width="1.3"
                            stroke-dasharray="2.5 2" />
                    </svg>
                </button>
            @endif

            {{-- Completar deshabilitado --}}
            @if ($pendingEnd)
                <button disabled title="Disponible al finalizar la cita ({{ $appt->end_time->format('H:i') }})"
                    class="w-[30px] h-[30px] flex items-center justify-center rounded-lg
                           bg-surface-container border border-outline-variant/20
                           text-on-surface-variant/30 cursor-not-allowed">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <circle cx="7" cy="7" r="6" stroke="currentColor" stroke-width="1.3"
                            stroke-dasharray="2.5 2" />
                        <path d="M4.5 7.2l2.3 2.3 3.7-4.5" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            @endif

            {{-- Cancelar --}}
            @if (in_array($appt->status, ['pending', 'confirmed']))
                <button wire:click="openCancelModal({{ $appt->id }})" title="Cancelar cita"
                    class="w-[30px] h-[30px] flex items-center justify-center rounded-lg
                           bg-[#FCEBEB] border border-[#F7C1C1] text-[#A32D2D]
                           hover:bg-[#E24B4A] hover:text-white hover:border-[#E24B4A]
                           transition-colors duration-150">
                    <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                        <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" />
                    </svg>
                </button>
            @endif

        </div>
    </td>
</tr>
