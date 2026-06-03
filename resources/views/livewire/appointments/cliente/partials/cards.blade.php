<div class="flex flex-col gap-3 md:hidden">
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
    && now()->lt($appt->start_time->subHours(2));
    @endphp
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30
                    shadow-[0_1px_4px_rgba(95,94,90,0.05)] p-4 flex flex-col gap-3">

        {{-- Top: empresa + badge --}}
        <div class="flex items-start justify-between gap-2">
            <p class="text-[14px] font-semibold text-primary leading-tight">
                {{ $appt->company?->name }}
            </p>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full
                             text-[10.5px] font-semibold shrink-0 {{ $badgeClass }}">
                {{ $badgeLabel }}
            </span>
        </div>

        {{-- Servicios --}}
        <div class="flex flex-wrap gap-1">
            @foreach ($appt->services as $s)
            <span class="px-2 py-0.5 rounded-md bg-surface-container
                                 text-on-surface-variant text-[11px] font-medium border border-outline-variant/20">
                {{ $s->name }}
            </span>
            @endforeach
        </div>

        {{-- Detalles --}}
        <div class="flex flex-col gap-1 text-[12px] text-on-surface-variant">
            <span>📅 {{ $appt->start_time->isoFormat('ddd D MMM YYYY · H:mm') }}</span>
            <span>👤 {{ $appt->user?->name ?? 'Sin asignar' }}</span>
            @if($appt->company?->address)
            <span>📍 {{ $appt->company->address }}</span>
            @endif
            @if($appt->company?->phone)
            <span>📞 {{ $appt->company->phone }}</span>
            @endif
        </div>

        {{-- Acción --}}
        @if ($cancellable)
        <button wire:click="openCancelModal({{ $appt->id }})"
            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl
                           text-[13px] font-semibold
                           bg-[#FCEBEB] text-[#A32D2D] border border-[#F7C1C1]
                           hover:bg-[#E24B4A] hover:text-white hover:border-[#E24B4A]
                           transition-colors duration-150">
            <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                <path d="M2 2l10 10M12 2L2 12" stroke="currentColor"
                    stroke-width="1.5" stroke-linecap="round" />
            </svg>
            Cancelar cita
        </button>
        @elseif (in_array($appt->status, ['pending', 'confirmed']))
        <div class="inline-flex items-center justify-center gap-1 text-[11px] text-on-surface-variant/60 py-1">
            <svg width="11" height="11" viewBox="0 0 14 14" fill="none">
                <rect x="3" y="6" width="8" height="6" rx="1.5" stroke="currentColor" stroke-width="1.5" />
                <path d="M5 6V4.5a2 2 0 014 0V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            Cancelación bloqueada (menos de 2h)
        </div>
        @endif
    </div>
    @empty
    <div class="flex flex-col items-center gap-3 py-16">
        <div class="w-12 h-12 rounded-full bg-surface-container
                        flex items-center justify-center text-on-surface-variant/50">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                <rect x="1" y="3" width="20" height="17" rx="3" stroke="currentColor" stroke-width="1.5" />
                <path d="M1 8h20M7 1v3M15 1v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
        <p class="text-[13px] text-on-surface-variant">No hay citas con los filtros aplicados.</p>
    </div>
    @endforelse
</div>