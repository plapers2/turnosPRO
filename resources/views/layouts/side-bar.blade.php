<!-- SideNavBar -->
<aside id="sidebar"
    class="fixed top-0 left-0 h-screen w-64 bg-[#f6f3ee] border-r border-stone-200/20 z-[60]
           transform -translate-x-full md:translate-x-0
           transition-transform duration-300">
    <!-- Brand Header -->
    <div class="px-6 py-8 flex flex-col gap-1">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-3xl text-[#854F0B]"
                style="font-variation-settings: 'FILL' 1;">calendar_month</span>
            <span class="text-xl font-bold tracking-tighter text-[#854F0B]">TurnosPRO</span>
        </div>
        <p class="text-xs text-on-surface-variant ml-10">Gestión de Turnos</p>
    </div>
    <!-- Navigation Links -->
    <div class="p-4 gap-2 flex flex-col h-full overflow-y-auto">

        <x-sidebar-link route="dashboard" pattern="dashboard" icon="dashboard">
            Dashboard
        </x-sidebar-link>

        {{-- <x-sidebar-link route="appointments.index" pattern="appointments.*" icon="calendar_month">
            Citas
        </x-sidebar-link> --}}

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

        @can('gestionar servicios')
        <x-sidebar-link route="services.index" pattern="services.*" icon="service_toolbox">
            Servicios
        </x-sidebar-link>
        @endcan

        @can('gestionar usuarios')
        <x-sidebar-link route="users.index" pattern="users.*" icon="group">
            Profesionales
        </x-sidebar-link>
        @endcan

        @can('gestionar clientes')
        <x-sidebar-link route="customers.index" pattern="customers.*" icon="group">
            Clientes
        </x-sidebar-link>
        @endcan

        {{--<x-sidebar-link route="settings.index" pattern="settings.*" icon="settings">
            Configuración
        </x-sidebar-link> --}}

    </div>

</aside>
<!-- Overlay (AQUÍ VA) -->
<div id="overlay"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden md:hidden z-50 transition-all duration-300">
</div>