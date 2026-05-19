{{-- resources/views/livewire/appointments/list/card.blade.php --}}
@php
    $canComplete = $appt->status === 'confirmed' && now()->gte($appt->end_time);
    $canCancel = in_array($appt->status, ['pending', 'confirmed']);
    $actionCount = (int) $canComplete + (int) $canCancel;
    $gridCols = match (true) {
        $actionCount === 0 => 'grid-cols-1',
        $actionCount === 1 => 'grid-cols-2',
        default => 'grid-cols-3',
    };
    $badgeClass = match ($appt->status) {
        'pending' => 'bg-[#FAEEDA] text-[#854F0B]',
        'confirmed' => 'bg-[#E1F5EE] text-[#0F6E56]',
        'cancelled' => 'bg-[#FCEBEB] text-[#A32D2D]',
        'completed' => 'bg-[#E6F1FB] text-[#185FA5]',
        default => 'bg-surface-container text-on-surface-variant',
    };
@endphp

<article wire:key="card-{{ $appt->id }}"
    class="relative bg-surface-container-lowest border border-outline-variant/40
           rounded-2xl p-4 flex flex-col gap-3
           shadow-[0_1px_6px_rgba(95,94,90,0.06)]
           hover:shadow-[0_4px_16px_rgba(95,94,90,0.10)]
           hover:-translate-y-0.5 transition-all duration-200">

    {{-- Acento estado --}}
    @php
        $accentColor = match ($appt->status) {
            'pending' => 'bg-[#854F0B]',
            'confirmed' => 'bg-[#0F6E56]',
            'cancelled' => 'bg-[#A32D2D]',
            'completed' => 'bg-[#185FA5]',
            default => 'bg-outline-variant',
        };
    @endphp
    <div class="absolute left-0 top-4 bottom-4 w-[3px] {{ $accentColor }} rounded-r-full"></div>

    {{-- Top: cliente + badge --}}
    <div class="flex items-start justify-between gap-3 pl-2">
        <div class="flex items-center gap-3 min-w-0">
            <div
                class="w-9 h-9 rounded-full bg-primary-fixed/30 text-primary
                        flex items-center justify-center text-[11px] font-bold flex-shrink-0">
                {{ strtoupper(substr($appt->customer->name, 0, 2)) }}
            </div>
            <div class="min-w-0">
                <p class="text-[13px] font-semibold text-on-surface tracking-tight leading-tight truncate">
                    {{ $appt->customer->name }}
                </p>
                <p class="text-[11px] text-on-surface-variant mt-0.5">{{ $appt->customer->phone }}</p>
            </div>
        </div>
        <span
            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                     flex-shrink-0 {{ $badgeClass }}">
            {{ __('appt.status.' . $appt->status) }}
        </span>
    </div>

    {{-- Meta --}}
    <div class="flex flex-col gap-1.5 pl-2">
        <div class="flex items-center gap-2 text-[11.5px] text-on-surface-variant">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" class="flex-shrink-0 text-primary/50">
                <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.3" />
                <path d="M6 3.5v2.8l1.5 1.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
            </svg>
            <span>{{ $appt->start_time->format('d/m/Y') }} · {{ $appt->start_time->format('H:i') }} –
                {{ $appt->end_time->format('H:i') }}</span>
        </div>
        <div class="flex items-center gap-2 text-[11.5px] text-on-surface-variant">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" class="flex-shrink-0 text-primary/50">
                <circle cx="6" cy="4" r="2" stroke="currentColor" stroke-width="1.3" />
                <path d="M1 11c0-2.2 2.2-4 5-4s5 1.8 5 4" stroke="currentColor" stroke-width="1.3"
                    stroke-linecap="round" />
            </svg>
            <span class="truncate">{{ $appt->user->name }}</span>
        </div>
    </div>

    {{-- Servicios --}}
    @if ($appt->services->count())
        <div class="flex flex-wrap gap-1 pl-2">
            @foreach ($appt->services->take(2) as $svc)
                <span
                    class="bg-surface-container border border-outline-variant/30
                             text-on-surface-variant text-[10px] font-medium px-2 py-0.5 rounded-lg">
                    {{ $svc->name }}
                </span>
            @endforeach
            @if ($appt->services->count() > 2)
                <span
                    class="bg-surface-container text-on-surface-variant text-[10px] font-medium px-2 py-0.5 rounded-lg">
                    +{{ $appt->services->count() - 2 }}
                </span>
            @endif
        </div>
    @endif

    {{-- Acciones --}}
    <div class="grid {{ $gridCols }} gap-1.5 pt-2 border-t border-outline-variant/20">

        <button wire:click="viewAppointment({{ $appt->id }})"
            class="flex items-center justify-center gap-1 py-2 px-2 rounded-xl
                   text-[11px] font-semibold text-on-surface-variant
                   border border-outline-variant/30 bg-surface-container
                   hover:bg-surface-container-high transition-colors duration-150">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                <circle cx="6" cy="6" r="2" stroke="currentColor" stroke-width="1.3" />
                <path d="M1 6s2-4 5-4 5 4 5 4-2 4-5 4-5-4-5-4z" stroke="currentColor" stroke-width="1.3" />
            </svg>
            Ver
        </button>

        @if ($canComplete)
            <button wire:click="openCompleteModal({{ $appt->id }})"
                class="flex items-center justify-center gap-1 py-2 px-2 rounded-xl
                       text-[11px] font-semibold text-[#185FA5]
                       bg-[#E6F1FB] border border-[#9EC8F0]
                       hover:bg-[#378ADD] hover:text-white hover:border-[#378ADD]
                       transition-colors duration-150">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.3"
                        stroke-dasharray="2.5 2" />
                    <path d="M3.5 6.2l2.2 2.2 3.5-4.3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Completar
            </button>
        @endif

        @role('admin')
            @if ($canCancel)
                <button wire:click="openCancelModal({{ $appt->id }})"
                    class="flex items-center justify-center gap-1 py-2 px-2 rounded-xl
                       text-[11px] font-semibold text-[#A32D2D]
                       bg-[#FCEBEB] border border-[#F7C1C1]
                       hover:bg-[#E24B4A] hover:text-white hover:border-[#E24B4A]
                       transition-colors duration-150">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M2 2l8 8M10 2L2 10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                    </svg>
                    Cancelar
                </button>
            @endif
        @endrole

    </div>
</article>
