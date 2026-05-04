<!-- SideNavBar -->
<aside id="sidebar"
    class="fixed top-0 left-0 h-screen w-64 bg-[#f6f3ee] border-r border-stone-200/20 z-[60]
           transform -translate-x-full md:translate-x-0
           transition-transform duration-300">
    <!-- Brand Header -->
    <div class="flex flex-col gap-1">
        <div class="flex items-center">
            <img src="{{ asset('turnos-pro.png') }}" alt="{{ config('app.name') }}"
                class="h-36 w-auto object-contain drop-shadow-md" />
        </div>
    </div>
    <!-- Navigation Links -->
    <div class="p-4 gap-2 flex flex-col h-full overflow-y-auto">

        <x-sidebar-link route="dashboard" pattern="dashboard" icon="dashboard">
            Dashboard
        </x-sidebar-link>


        @role(['admin', 'empleado', 'cliente'])
        <x-sidebar-link route="appointment-manager.index" pattern="appointment-manager.*" icon="event_note">
            Citas
        </x-sidebar-link>
        @endrole

        @can('gestionar usuarios')
        <x-sidebar-link route="users.index" pattern="users.*" icon="workspace_premium">
            Profesionales
        </x-sidebar-link>
        @endcan

        @can('gestionar servicios')
        <x-sidebar-link route="services.index" pattern="services.*" icon="service_toolbox">
            Servicios
        </x-sidebar-link>
        @endcan

        @can('gestionar clientes')
        <x-sidebar-link route="customers.index" pattern="customers.*" icon="groups">
            Clientes
        </x-sidebar-link>
        @endcan

        @can('gestionar empresas')
        <x-sidebar-link route="companies.index" pattern="companies.*" icon="business">
            Empresa
        </x-sidebar-link>
        @endcan

        @can('gestionar empresas')
        <x-sidebar-link route="type-companies.index" pattern="type-companies.*" icon="add_business">
            Tipos de Empresa
        </x-sidebar-link>
        @endcan

        @can('gestionar empresas')
        <x-sidebar-link route="opening-hours.index" pattern="opening-hours.*" icon="calendar_month">
            Horarios de atención
        </x-sidebar-link>
        @endcan

        @can('reservar citas')
        <x-sidebar-link route="appointment.index" pattern="appointment.index" icon="calendar_add_on">
            Reservar Cita
        </x-sidebar-link>
        @endcan

        @can('ver historial de citas')
        <x-sidebar-link route="appointment.history" pattern="appointment.history" icon="history">
            Historial de Citas
        </x-sidebar-link>
        @endcan

    </div>

</aside>
<!-- Overlay (AQUÍ VA) -->
<div id="overlay"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden md:hidden z-50 transition-all duration-300">
</div>