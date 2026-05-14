<x-app-layout>

    <div>
        @if (auth()->user()->hasRole('cliente'))
            @livewire('dashboard.cliente-dashboard')
        @elseif(auth()->user()->hasRole('empleado'))
            @livewire('dashboard.empleado-dashboard')
        @else
            @livewire('dashboard.admin-dashboard')
        @endif
    </div>
</x-app-layout>
