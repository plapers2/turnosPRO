{{-- resources/views/livewire/appointments/partials/header.blade.php --}}
{{-- Props: $total (int) --}}
<header
    class="relative mx-0 mb-5 overflow-hidden rounded-2xl border border-outline-variant/30
           bg-surface-container-lowest px-6 py-5
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
                <span
                    class="inline-flex items-center justify-center rounded-full
                             bg-primary-fixed/30 text-primary text-[11px] font-bold
                             px-2 py-0.5 min-w-[24px]">
                    {{ $total }}
                </span>
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
        <button @click="view = 'list'"
            :class="view === 'list'
                ?
                'bg-surface-container-lowest text-primary shadow-sm border border-outline-variant/30' :
                'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface border border-transparent'"
            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-[12.5px] font-semibold transition-all duration-150"
            title="Vista lista">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="3" width="14" height="2" rx="1" fill="currentColor" />
                <rect x="1" y="7" width="14" height="2" rx="1" fill="currentColor" />
                <rect x="1" y="11" width="14" height="2" rx="1" fill="currentColor" />
            </svg>
            <span>Lista</span>
        </button>

        <button @click="view = 'calendar'"
            :class="view === 'calendar'
                ?
                'bg-surface-container-lowest text-primary shadow-sm border border-outline-variant/30' :
                'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface border border-transparent'"
            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-[12.5px] font-semibold transition-all duration-150"
            title="Vista calendario">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="2" width="14" height="13" rx="2" stroke="currentColor" stroke-width="1.5"
                    fill="none" />
                <path d="M1 6h14" stroke="currentColor" stroke-width="1.5" />
                <path d="M5 1v2M11 1v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <span>Calendario</span>
        </button>
    </div>

</header>
