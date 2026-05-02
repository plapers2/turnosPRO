{{-- resources/views/livewire/appointments/list/card.blade.php --}}
@php
    $canComplete = $appt->status === 'confirmed' && now()->gte($appt->end_time);
    $canConfirm = $appt->status === 'pending';
    $canCancel = in_array($appt->status, ['pending', 'confirmed']);

    $actionCount = (int) $canConfirm + (int) $canComplete + (int) $canCancel;
    $gridCols = match (true) {
        $actionCount === 0 => 'grid-cols-1',
        $actionCount === 1 => 'grid-cols-2',
        default => 'grid-cols-3',
    };
@endphp

<article wire:key="card-{{ $appt->id }}"
    class="relative bg-white border border-outline-variant/20
           rounded-xl p-4 flex flex-col gap-3 shadow-sm
           hover:shadow-md transition-shadow duration-200">
    {{-- Top: cliente + badge --}}
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div
                class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center
                        text-primary text-sm font-bold flex-shrink-0">
                {{ strtoupper(substr($appt->customer->name, 0, 2)) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold text-on-surface tracking-tight leading-tight truncate">
                    {{ $appt->customer->name }}
                </p>
                <p class="text-xs text-on-surface-variant mt-0.5">{{ $appt->customer->phone }}</p>
            </div>
        </div>
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
            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                     flex-shrink-0 {{ $badgeClass }}">
            {{ __('appt.status.' . $appt->status) }}
        </span>
    </div>

    {{-- Meta --}}
    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2 text-xs text-on-surface-variant">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" class="flex-shrink-0 text-primary/50">
                <circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.3" />
                <path d="M6.5 4v2.8l1.5 1.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
            </svg>
            <span>{{ $appt->start_time->format('d/m/Y') }} · {{ $appt->start_time->format('H:i') }} –
                {{ $appt->end_time->format('H:i') }}</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-on-surface-variant">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" class="flex-shrink-0 text-primary/50">
                <circle cx="6.5" cy="4.5" r="2" stroke="currentColor" stroke-width="1.3" />
                <path d="M1.5 11c0-2.2 2.2-4 5-4s5 1.8 5 4" stroke="currentColor" stroke-width="1.3"
                    stroke-linecap="round" />
            </svg>
            <span class="truncate">{{ $appt->user->name }}</span>
        </div>
    </div>

    {{-- Servicios --}}
    @if ($appt->services->count())
        <div class="flex flex-wrap gap-1">
            @foreach ($appt->services->take(2) as $svc)
                <span
                    class="bg-surface-container border border-outline-variant/20
                             text-on-surface-variant text-[10px] font-medium px-2 py-0.5 rounded-md">
                    {{ $svc->name }}
                </span>
            @endforeach
            @if ($appt->services->count() > 2)
                <span
                    class="bg-surface-container text-on-surface-variant text-[10px] font-medium px-2 py-0.5 rounded-md">
                    +{{ $appt->services->count() - 2 }}
                </span>
            @endif
        </div>
    @endif

    {{-- Acciones: grid adaptativo según cuántos botones hay --}}
    <div class="grid {{ $gridCols }} gap-1.5 pt-2 border-t border-outline-variant/20">

        {{-- Ver --}}
        <button wire:click="viewAppointment({{ $appt->id }})"
            class="flex items-center justify-center gap-1 py-2 px-2 rounded-lg
                   text-[11px] font-semibold text-on-surface-variant
                   border border-outline-variant/30 bg-white
                   hover:bg-surface-container transition-colors duration-150">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                <circle cx="6" cy="6" r="2" stroke="currentColor" stroke-width="1.3" />
                <path d="M1 6s2-4 5-4 5 4 5 4-2 4-5 4-5-4-5-4z" stroke="currentColor" stroke-width="1.3" />
            </svg>
            Ver
        </button>

        {{-- Confirmar --}}
        @if ($canConfirm)
            <button wire:click="confirmAppointment({{ $appt->id }})"
                class="flex items-center justify-center gap-1 py-2 px-2 rounded-lg
                       text-[11px] font-semibold text-[#0F6E56]
                       bg-[#E1F5EE] border border-[#9FE1CB]
                       hover:bg-[#1D9E75] hover:text-white hover:border-[#1D9E75] transition-colors duration-150">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <path d="M1.5 6l3 3 6-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Confirmar
            </button>
        @endif

        {{-- RF-26: Completar --}}
        @if ($canComplete)
            <button wire:click="openCompleteModal({{ $appt->id }})"
                class="flex items-center justify-center gap-1 py-2 px-2 rounded-lg
                       text-[11px] font-semibold text-[#185FA5]
                       bg-[#E6F1FB] border border-[#9EC8F0]
                       hover:bg-[#378ADD] hover:text-white hover:border-[#378ADD] transition-colors duration-150">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <path d="M1 6.5l2.5 2.5 7-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.2"
                        stroke-dasharray="2.5 1.8" />
                </svg>
                Completar
            </button>
        @endif

        {{-- Cancelar --}}
        @if ($canCancel)
            <button wire:click="openCancelModal({{ $appt->id }})"
                class="flex items-center justify-center gap-1 py-2 px-2 rounded-lg
                       text-[11px] font-semibold text-[#A32D2D]
                       bg-[#FCEBEB] border border-[#F7C1C1]
                       hover:bg-[#E24B4A] hover:text-white hover:border-[#E24B4A] transition-colors duration-150">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <path d="M2 2l8 8M10 2L2 10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                </svg>
                Cancelar
            </button>
        @endif

    </div>
</article>
