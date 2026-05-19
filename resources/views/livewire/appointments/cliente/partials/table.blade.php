<div class="hidden md:block bg-surface-container-lowest rounded-2xl
            border border-outline-variant/40 shadow-[0_1px_6px_rgba(95,94,90,0.06)] overflow-hidden">
    <table class="w-full border-collapse text-sm">
        <thead>
            <tr class="border-b border-outline-variant/30 bg-surface-container/60">
                @foreach (['Empresa', 'Servicios', 'Profesional', 'Fecha y hora', 'Estado', 'Acción'] as $col)
                <th class="px-4 py-3 text-left text-[10.5px] font-semibold
                               text-on-surface-variant uppercase tracking-wider">
                    {{ $col }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $appt)
            @php
            $badgeClass = match ($appt->status) {
            'pending' => 'bg-[#FAEEDA] text-[#854F0B]',
            'confirmed' => 'bg-[#E1F5EE] text-[#0F6E56]',
            'completed' => 'bg-[#E6F1FB] text-[#185FA5]',
            'cancelled' => 'bg-[#FCEBEB] text-[#A32D2D]',
            default => 'bg-surface-container text-on-surface-variant',
            };
            $badgeLabel = match ($appt->status) {
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            default => $appt->status,
            };
            $cancellable = in_array($appt->status, ['pending', 'confirmed'])
            && $appt->start_time->copy()->subHours(2)->gt(now());
            @endphp
            <tr class="border-b border-outline-variant/10 hover:bg-surface/40 transition-colors">
                <td class="px-4 py-3 font-semibold text-primary text-[13px]">
                    {{ $appt->company?->name }}
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        @foreach ($appt->services as $s)
                        <span class="px-2 py-0.5 rounded-md bg-surface-container
                                             text-on-surface-variant text-[11px] font-medium border border-outline-variant/20">
                            {{ $s->name }}
                        </span>
                        @endforeach
                    </div>
                </td>
                <td class="px-4 py-3 text-[13px] text-on-surface-variant">
                    {{ $appt->user?->name ?? 'Sin asignar' }}
                </td>
                <td class="px-4 py-3 text-[13px] text-on-surface-variant whitespace-nowrap">
                    {{ $appt->start_time->isoFormat('ddd D MMM YYYY · H:mm') }}
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                     text-[10.5px] font-semibold {{ $badgeClass }}">
                        {{ $badgeLabel }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    @if ($cancellable)
                    <button wire:click="openCancelModal({{ $appt->id }})"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                       text-[12px] font-semibold
                                       bg-[#FCEBEB] text-[#A32D2D] border border-[#F7C1C1]
                                       hover:bg-[#E24B4A] hover:text-white hover:border-[#E24B4A]
                                       transition-colors duration-150">
                        <svg width="12" height="12" viewBox="0 0 14 14" fill="none">
                            <path d="M2 2l10 10M12 2L2 12" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        Cancelar
                    </button>
                    @elseif (in_array($appt->status, ['pending', 'confirmed']))
                    <span class="inline-flex items-center gap-1 text-[11px] text-on-surface-variant/60">
                        <svg width="11" height="11" viewBox="0 0 14 14" fill="none">
                            <rect x="3" y="6" width="8" height="6" rx="1.5" stroke="currentColor" stroke-width="1.5" />
                            <path d="M5 6V4.5a2 2 0 014 0V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        Bloqueada
                    </span>
                    @else
                    <span class="text-[11px] text-on-surface-variant/40">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-16 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-surface-container
                                        flex items-center justify-center text-on-surface-variant/50">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                                <rect x="1" y="3" width="20" height="17" rx="3" stroke="currentColor" stroke-width="1.5" />
                                <path d="M1 8h20M7 1v3M15 1v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                        </div>
                        <p class="text-[13px] text-on-surface-variant">No hay citas con los filtros aplicados.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>