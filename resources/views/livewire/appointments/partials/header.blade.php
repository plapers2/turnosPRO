{{-- resources/views/livewire/appointments/partials/header.blade.php --}}
{{-- Props: $total (int) --}}
<header
    class="relative bg-[#fcf9f3]/80 backdrop-blur-md border border-outline-variant/20
               rounded-xl mb-4 px-6 py-5 flex flex-col lg:flex-row
               items-start lg:items-center justify-between gap-4 shadow-[0_8px_20px_rgba(95,94,90,0.04)]">

    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">calendar_month</span>
            </div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold text-primary tracking-tight">Citas</h2>
                <span class="bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full">
                    {{ $total }}
                </span>
            </div>
        </div>
        <p class="text-sm text-on-surface-variant ml-13">
            Gestiona y hace seguimiento de todas las citas agendadas.
        </p>
    </div>

    <div class="flex items-center gap-2 bg-surface-container rounded-lg p-1">
        <button @click="view = 'list'"
            :class="view === 'list'
                ?
                'bg-white text-primary shadow-sm' :
                'text-on-surface-variant hover:text-on-surface hover:bg-white/50'"
            class="flex items-center gap-1.5 px-4 py-2 rounded-md text-sm font-semibold transition-all duration-150"
            title="Vista lista">
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="3" width="14" height="2" rx="1" fill="currentColor" />
                <rect x="1" y="7" width="14" height="2" rx="1" fill="currentColor" />
                <rect x="1" y="11" width="14" height="2" rx="1" fill="currentColor" />
            </svg>
            <span>Lista</span>
        </button>

        <button @click="view = 'calendar'"
            :class="view === 'calendar'
                ?
                'bg-white text-primary shadow-sm' :
                'text-on-surface-variant hover:text-on-surface hover:bg-white/50'"
            class="flex items-center gap-1.5 px-4 py-2 rounded-md text-sm font-semibold transition-all duration-150"
            title="Vista calendario">
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                <rect x="1" y="2" width="14" height="13" rx="2" stroke="currentColor" stroke-width="1.5"
                    fill="none" />
                <path d="M1 6h14" stroke="currentColor" stroke-width="1.5" />
                <path d="M5 1v2M11 1v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <span>Calendario</span>
        </button>
    </div>

</header>
