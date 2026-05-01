{{-- resources/views/livewire/appointments/list/card.blade.php --}}
{{-- Props: $appt (Appointment) --}}
<article
    wire:key="card-{{ $appt->id }}"
    class="relative bg-white backdrop-blur-md border border-outline-variant/20
           rounded-xl p-5 flex flex-col gap-4 shadow
           hover:shadow-lg transition-shadow duration-200 my-3"
>
    {{-- Top: cliente + badge --}}
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center
                        text-primary text-sm font-bold flex-shrink-0">
                {{ strtoupper(substr($appt->customer->name, 0, 2)) }}
            </div>
            <div>
                <p class="text-sm font-bold text-on-surface tracking-tight leading-tight">
                    {{ $appt->customer->name }}
                </p>
                <p class="text-xs text-on-surface-variant mt-0.5">
                    {{ $appt->customer->phone }}
                </p>
            </div>
        </div>
        <span class="status-badge status-badge--{{ $appt->status }} flex-shrink-0">
            {{ __('appt.status.' . $appt->status) }}
        </span>
    </div>

    {{-- Meta: fecha + profesional --}}
    <div class="flex flex-col gap-1.5">
        <div class="flex items-center gap-2 text-xs text-on-surface-variant">
            <span class="material-symbols-outlined text-[15px] text-primary/60">schedule</span>
            <span>{{ $appt->start_time->format('d/m/Y') }} · {{ $appt->start_time->format('H:i') }} – {{ $appt->end_time->format('H:i') }}</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-on-surface-variant">
            <span class="material-symbols-outlined text-[15px] text-primary/60">person</span>
            <span>{{ $appt->user->name }}</span>
        </div>
    </div>

    {{-- Servicios --}}
    @if ($appt->services->count())
        <div class="flex flex-wrap gap-1.5">
            @foreach ($appt->services->take(3) as $svc)
                <span class="bg-primary/8 text-primary text-[11px] font-semibold px-2.5 py-0.5 rounded-md">
                    {{ $svc->name }}
                </span>
            @endforeach
            @if ($appt->services->count() > 3)
                <span class="bg-surface-container text-on-surface-variant text-[11px] font-semibold px-2.5 py-0.5 rounded-md">
                    +{{ $appt->services->count() - 3 }}
                </span>
            @endif
        </div>
    @endif

    {{-- Acciones --}}
    <div class="flex items-center gap-2 pt-1 border-t border-outline-variant/20">
        <button
            wire:click="viewAppointment({{ $appt->id }})"
            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                   text-xs font-semibold text-on-surface-variant border border-outline-variant/30
                   bg-white hover:bg-surface-container transition-colors duration-150"
        >
            <span class="material-symbols-outlined text-[14px]">open_in_new</span>
            Ver
        </button>

        @if ($appt->status === 'pending')
            <button
                wire:click="confirmAppointment({{ $appt->id }})"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                       text-xs font-semibold text-[#1d9e75] bg-[#e1f5ee] border border-[#1d9e75]/20
                       hover:bg-[#1d9e75] hover:text-white transition-colors duration-150"
            >
                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                Confirmar
            </button>
        @endif

        @if (in_array($appt->status, ['pending', 'confirmed']))
            <button
                wire:click="openCancelModal({{ $appt->id }})"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                       text-xs font-semibold text-[#e24b4a] bg-[#fcebeb] border border-[#e24b4a]/20
                       hover:bg-[#e24b4a] hover:text-white transition-colors duration-150"
            >
                <span class="material-symbols-outlined text-[14px]">cancel</span>
                Cancelar
            </button>
        @endif
    </div>
</article>
