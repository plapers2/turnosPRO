{{-- resources/views/livewire/appointments/partials/header.blade.php --}}
{{-- Props: $total (int) --}}
<header
    class="relative mx-0 mb-5 overflow-hidden rounded-2xl border border-outline-variant/30
           bg-surface-container-lowest px-6 py-7
           flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4
           shadow-[0_1px_8px_rgba(95,94,90,0.06)]">

    {{-- Acento izquierdo --}}
    <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-primary rounded-l-2xl"></div>

    {{-- Ícono + texto --}}
    <div class="flex items-center gap-4 pl-2">
        <div
            class="flex h-[42px] w-[42px] shrink-0 items-center justify-center
                    rounded-xl border border-primary-fixed-dim/40 bg-primary-fixed/20 text-primary">
            <span class="material-symbols-outlined text-[20px]">calendar_month</span>
        </div>
        <div class="flex flex-col gap-0.5">
            <div class="flex items-center gap-2">
                <h2 class="text-[17px] font-semibold leading-tight tracking-tight text-on-surface">
                    Citas
                </h2>
            </div>
            <p class="text-[13px] text-on-surface-variant">
                Gestiona y hace seguimiento de todas las citas agendadas.
            </p>
        </div>
    </div>

    {{-- Toggle de vista --}}
    <div
        class="flex items-center gap-1 rounded-xl border border-outline-variant/40
                bg-surface-container p-1 shrink-0">
        <button wire:click="$set('view', 'list')" @class([
            'flex items-center gap-1.5 px-4 py-2 rounded-lg text-[12.5px] font-semibold transition-all duration-150',
            'bg-surface-container-lowest text-primary shadow-sm border border-outline-variant/30' =>
                $view === 'list',
            'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface border border-transparent' =>
                $view !== 'list',
        ]) title="Vista lista">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="3" width="14" height="2" rx="1" fill="currentColor" />
                <rect x="1" y="7" width="14" height="2" rx="1" fill="currentColor" />
                <rect x="1" y="11" width="14" height="2" rx="1" fill="currentColor" />
            </svg>
            <span>Lista</span>
        </button>

        <button wire:click="$set('view', 'calendar')" @class([
            'flex items-center gap-1.5 px-4 py-2 rounded-lg text-[12.5px] font-semibold transition-all duration-150',
            'bg-surface-container-lowest text-primary shadow-sm border border-outline-variant/30' =>
                $view === 'calendar',
            'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface border border-transparent' =>
                $view !== 'calendar',
        ]) title="Vista calendario">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="2" width="14" height="13" rx="2" stroke="currentColor" stroke-width="1.5"
                    fill="none" />
                <path d="M1 6h14" stroke="currentColor" stroke-width="1.5" />
                <path d="M5 1v2M11 1v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <span>Calendario</span>
        </button>

        <button wire:click="$set('view', 'availability')" @class([
            'inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors',
            'bg-surface-container-lowest text-primary shadow-sm border border-outline-variant/30' =>
                $view === 'availability',
            'text-gray-600 hover:bg-gray-100' => $view !== 'availability',
        ])>
            {{-- Ícono: cuadrícula con check --}}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                 m-6 9l2 2 4-4" />
            </svg>
            Disponibilidad
            {{-- Badge verde si hay slots libres en la semana actual --}}
            @if (isset($availabilitySummary['free_slots']) && $availabilitySummary['free_slots'] > 0)
                <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-700 rounded-full">
                    {{ $availabilitySummary['free_slots'] }}
                </span>
            @endif
        </button>

        @if ($isAdmin)
            <button wire:click="openDelayModal"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl
           bg-[#FAEEDA] text-[#854F0B] border border-[#FAC775]
           text-[12.5px] font-semibold
           hover:bg-[#FAC775] transition-colors duration-150">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                    <path d="M8 5v3.5l2 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                Notificar retraso
            </button>
        @endif

        <a href="{{ route('employee.appointment.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Nueva cita
        </a>
    </div>


</header>
