{{-- resources/views/livewire/appointments/list/table-row.blade.php --}}
{{-- Props: $appt (Appointment) --}}
<tr wire:key="row-{{ $appt->id }}"
    class="border-b border-outline-variant/20 last:border-b-0
           hover:bg-surface-container/50 transition-colors duration-100">

    {{-- Cliente --}}
    <td class="px-4 py-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-[#E6F1FB] text-[#185FA5]
                        flex items-center justify-center text-[11px] font-semibold flex-shrink-0">
                {{ strtoupper(substr($appt->customer->name, 0, 2)) }}
            </div>
            <div>
                <p class="text-sm font-semibold text-on-surface leading-tight">
                    {{ $appt->customer->name }}
                </p>
                <p class="text-xs text-on-surface-variant mt-0.5">
                    {{ $appt->customer->phone }}
                </p>
            </div>
        </div>
    </td>

    {{-- Profesional --}}
    <td class="px-4 py-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-[#E1F5EE] text-[#0F6E56]
                        flex items-center justify-center text-[11px] font-semibold flex-shrink-0">
                {{ strtoupper(substr($appt->user->name, 0, 2)) }}
            </div>
            <span class="text-sm text-on-surface">{{ $appt->user->name }}</span>
        </div>
    </td>

    {{-- Servicios --}}
    <td class="px-4 py-3">
        <div class="flex flex-wrap gap-1">
            @foreach ($appt->services->take(2) as $svc)
                <span class="inline-block bg-surface-container border border-outline-variant/30
                             text-on-surface-variant text-[11px] font-medium px-2 py-0.5 rounded-md">
                    {{ $svc->name }}
                </span>
            @endforeach
            @if ($appt->services->count() > 2)
                <span class="inline-block bg-[#F1EFE8] text-[#5F5E5A] border border-[#D3D1C7]
                             text-[11px] font-medium px-2 py-0.5 rounded-md">
                    +{{ $appt->services->count() - 2 }}
                </span>
            @endif
        </div>
    </td>

    {{-- Fecha y hora --}}
    <td class="px-4 py-3">
        <p class="text-sm font-semibold text-on-surface leading-tight">
            {{ $appt->start_time->format('d/m/Y') }}
        </p>
        <p class="text-xs text-on-surface-variant mt-0.5">
            {{ $appt->start_time->format('H:i') }} – {{ $appt->end_time->format('H:i') }}
        </p>
    </td>

    {{-- Duración --}}
    <td class="px-4 py-3">
        <span class="text-xs text-on-surface-variant">
            {{ $appt->start_time->diffInMinutes($appt->end_time) }} min
        </span>
    </td>

    {{-- Estado --}}
    <td class="px-4 py-3">
        @php
            $badgeClass = match($appt->status) {
                'pending'   => 'bg-[#FAEEDA] text-[#854F0B]',
                'confirmed' => 'bg-[#E1F5EE] text-[#0F6E56]',
                'cancelled' => 'bg-[#FCEBEB] text-[#A32D2D]',
                'completed' => 'bg-[#E6F1FB] text-[#185FA5]',
                default     => 'bg-surface-container text-on-surface-variant',
            };
        @endphp
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
            {{ __('appt.status.' . $appt->status) }}
        </span>
    </td>

    {{-- Acciones --}}
    <td class="px-4 py-3">
        <div class="flex items-center gap-1.5">
            <button
                wire:click="viewAppointment({{ $appt->id }})"
                title="Ver detalle"
                class="w-[30px] h-[30px] flex items-center justify-center rounded-lg
                       bg-surface-container border border-outline-variant/20
                       text-on-surface-variant hover:bg-surface-container-high
                       transition-colors duration-150"
            >
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <circle cx="7" cy="7" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M1 7s2-4.5 6-4.5S13 7 13 7s-2 4.5-6 4.5S1 7 1 7z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>

            @if ($appt->status === 'pending')
                <button
                    wire:click="confirmAppointment({{ $appt->id }})"
                    title="Confirmar"
                    class="w-[30px] h-[30px] flex items-center justify-center rounded-lg
                           bg-[#E1F5EE] border border-[#9FE1CB]
                           text-[#0F6E56] hover:bg-[#1D9E75] hover:text-white hover:border-[#1D9E75]
                           transition-colors duration-150"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M2 7l3.5 3.5L12 4" stroke="currentColor" stroke-width="1.5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            @endif

            @if (in_array($appt->status, ['pending', 'confirmed']))
                <button
                    wire:click="openCancelModal({{ $appt->id }})"
                    title="Cancelar"
                    class="w-[30px] h-[30px] flex items-center justify-center rounded-lg
                           bg-[#FCEBEB] border border-[#F7C1C1]
                           text-[#A32D2D] hover:bg-[#E24B4A] hover:text-white hover:border-[#E24B4A]
                           transition-colors duration-150"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
            @endif
        </div>
    </td>

</tr>
