<x-app-layout>

    <div>
        @if (auth()->user()->hasRole('admin'))
            @livewire('dashboard.admin-dashboard')
        @elseif(auth()->user()->hasRole('empleado'))
            @livewire('dashboard.empleado-dashboard')
        @else
            @php
                header('Location: /appointments');
                exit();
            @endphp
        @endif
    </div>
</x-app-layout>
