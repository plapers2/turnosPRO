{{-- resources/views/livewire/appointments/partials/filters-bar.blade.php --}}
{{-- Props: $professionals (Collection<User>) --}}
<section class="filters-bar">

    {{-- Búsqueda --}}
    <div class="filter-group filter-group--search">
        <svg class="filter-group__icon" width="14" height="14" viewBox="0 0 14 14" fill="none">
            <circle cx="6" cy="6" r="4.5" stroke="currentColor" stroke-width="1.5"/>
            <path d="M9.5 9.5L13 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <input
            wire:model.live.debounce.300ms="search"
            type="search"
            placeholder="Buscar cliente o profesional…"
            class="filter-input filter-input--search"
        />
    </div>

    {{-- Profesional --}}
    <div class="filter-group">
        <select wire:model.live="filterProfessional" class="filter-select">
            <option value="">Todos los profesionales</option>
            @foreach ($professionals as $pro)
                <option value="{{ $pro->id }}">{{ $pro->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Estado --}}
    <div class="filter-group">
        <select wire:model.live="filterStatus" class="filter-select">
            <option value="">Todos los estados</option>
            <option value="pending">Pendiente</option>
            <option value="confirmed">Confirmada</option>
            <option value="completed">Completada</option>
            <option value="cancelled">Cancelada</option>
        </select>
    </div>

    {{-- Rango de fechas --}}
    <div class="filter-group filter-group--dates">
        <input
            wire:model.live="filterDateFrom"
            type="date"
            class="filter-input"
            title="Desde"
        />
        <span class="filter-group__sep">—</span>
        <input
            wire:model.live="filterDateTo"
            type="date"
            class="filter-input"
            title="Hasta"
        />
    </div>

    {{-- Reset --}}
    <button
        wire:click="$set('filterProfessional', null); $set('filterStatus', ''); $set('search', ''); $set('filterDateFrom', ''); $set('filterDateTo', '')"
        class="btn-reset"
        title="Limpiar filtros"
    >
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
            <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </button>

</section>
