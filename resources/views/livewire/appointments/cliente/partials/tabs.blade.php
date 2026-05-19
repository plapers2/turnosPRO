<div class="flex items-center gap-1 mb-4 p-1 bg-surface-container rounded-xl border border-outline-variant/30 w-fit">

    <button wire:click="setTab('proximas')"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-[13px] font-semibold transition-all duration-150
               {{ $activeTab === 'proximas'
                   ? 'bg-surface-container-lowest text-primary shadow-sm border border-outline-variant/30'
                   : 'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface border border-transparent' }}">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
            <path d="M8 5v3.5l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Próximas
        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold
                     {{ $activeTab === 'proximas' ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">
            {{ $countProximas }}
        </span>
    </button>

    <button wire:click="setTab('historial')"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-[13px] font-semibold transition-all duration-150
               {{ $activeTab === 'historial'
                   ? 'bg-surface-container-lowest text-primary shadow-sm border border-outline-variant/30'
                   : 'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface border border-transparent' }}">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <path d="M8 1v7l4 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
        </svg>
        Historial
        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold
                     {{ $activeTab === 'historial' ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">
            {{ $countHistorial }}
        </span>
    </button>

</div>