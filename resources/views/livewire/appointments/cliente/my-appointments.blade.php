<div>
    @include('livewire.appointments.cliente.partials.header')
    @include('livewire.appointments.cliente.partials.stats-row', ['stats' => $stats])

    {{-- TABS --}}
    @include('livewire.appointments.cliente.partials.tabs', [
    'countProximas' => $countProximas,
    'countHistorial' => $countHistorial,
    ])

    @include('livewire.appointments.cliente.partials.filters-bar')

    <div wire:loading class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    @include('livewire.appointments.cliente.partials.table', ['appointments' => $appointments])
    @include('livewire.appointments.cliente.partials.cards', ['appointments' => $appointments])
    @include('livewire.appointments.partials.pagination', ['paginator' => $appointments])

    @if ($showCancelModal)
    @include('livewire.appointments.cliente.partials.cancel-modal')
    @endif
</div>