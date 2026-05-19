<section class="flex flex-wrap items-center gap-2.5 p-4 mb-4
                bg-surface-container-lowest rounded-xl
                border border-outline-variant/20 shadow-sm">

    {{-- Búsqueda --}}
    <div class="flex items-center gap-2 flex-1 min-w-[160px]
                bg-surface-container rounded-lg border border-outline-variant/20 px-3 h-9">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" class="text-on-surface-variant flex-shrink-0">
            <circle cx="6" cy="6" r="4.5" stroke="currentColor" stroke-width="1.5" />
            <path d="M9.5 9.5L13 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        <input wire:model.live.debounce.300ms="search" type="search"
            placeholder="Buscar por empresa o servicio…"
            class="bg-transparent border-none outline-none text-sm text-on-surface
                   placeholder:text-on-surface-variant w-full" />
    </div>

    {{-- Estado — solo visible en historial --}}
    @if ($activeTab === 'historial')
    <select wire:model.live="filterStatus"
        class="h-9 bg-surface-container rounded-lg border border-outline-variant/20
           px-3 text-sm text-on-surface outline-none cursor-pointer">
        <option value="">Todos los estados</option>
        <option value="completed">Completada</option>
        <option value="cancelled">Cancelada</option>
    </select>
    @endif

    {{-- Rango de fechas --}}
    <div class="flex items-center gap-2">
        <input wire:model.live="filterDateFrom" type="date" title="Desde"
            class="h-9 bg-surface-container rounded-lg border border-outline-variant/20
                   px-3 text-sm text-on-surface outline-none cursor-pointer" />
        <span class="text-xs text-on-surface-variant">—</span>
        <input wire:model.live="filterDateTo" type="date" title="Hasta"
            class="h-9 bg-surface-container rounded-lg border border-outline-variant/20
                   px-3 text-sm text-on-surface outline-none cursor-pointer" />
    </div>

    {{-- Reset --}}
    <button wire:click="resetFilters" title="Limpiar filtros"
        class="w-9 h-9 flex items-center justify-center flex-shrink-0
               bg-surface-container rounded-lg border border-outline-variant/20
               text-on-surface-variant
               hover:bg-error/10 hover:text-error hover:border-error/30
               transition-colors duration-150">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
            <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
        </svg>
    </button>
</section>