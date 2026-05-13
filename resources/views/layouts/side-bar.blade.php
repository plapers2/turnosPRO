<!-- SideNavBar -->
<aside id="sidebar"
    class="fixed top-0 left-0 h-screen w-60 z-[70]
           transform -translate-x-full md:translate-x-0
           transition-transform duration-300 ease-in-out
           flex flex-col overflow-hidden
           bg-white border">

    {{-- Brand Header con Logo --}}
    <div class="flex flex-col items-center px-4 pt-5 pb-4 shrink-0">

        {{-- Logo --}}
        <div class="relative group flex items-center justify-center w-full">
            <div
                class="absolute inset-0 rounded-xl opacity-0 blur-md transition-all duration-500 group-hover:opacity-30 pointer-events-none">
            </div>
            <img src="{{ asset('turnos-pro.png') }}" alt="{{ config('app.name') }}"
                class="relative h-20 w-auto object-contain transition-transform duration-300 group-hover:scale-105" />
        </div>

        {{-- Subtítulo opcional debajo del logo --}}
        <span class="mt-1 text-[10px] font-medium tracking-widest uppercase"
            style="color: #847467; letter-spacing: 0.12em;">
            Panel de gestión
        </span>
    </div>

    {{-- Navigation Links --}}
    <nav
        class="flex-1 overflow-y-auto px-2.5 py-2.5 space-y-px
                scrollbar-thin scrollbar-thumb-outline-variant scrollbar-track-transparent">

        @can('gestionar plataforma')
            <x-sidebar-link route="master.index" pattern="master.index" icon="admin_panel_settings">
                Empresas
            </x-sidebar-link>

            <x-sidebar-link route="master.type-companies.index" pattern="master.type-companies.*" icon="add_business">
                Tipos de Empresa
            </x-sidebar-link>
        @endcan

        @can('gestionar dashboard')
            <x-sidebar-link route="dashboard" pattern="dashboard" icon="dashboard">
                Dashboard
            </x-sidebar-link>
        @endcan

        @can('gestionar citas')
            <x-sidebar-link route="appointment-manager.index" pattern="appointment-manager.*" icon="event_note">
                Citas
            </x-sidebar-link>
        @endcan

        @can('gestionar usuarios')
            <x-sidebar-link route="users.index" pattern="users.*" icon="workspace_premium">
                Profesionales
            </x-sidebar-link>
        @endcan

        @canany(['gestionar servicios', 'ver servicios'])
            <x-sidebar-link route="services.index" pattern="services.*" icon="service_toolbox">
                Servicios
            </x-sidebar-link>
        @endcanany

        @can('gestionar clientes')
            <x-sidebar-link route="customers.index" pattern="customers.*" icon="groups">
                Clientes
            </x-sidebar-link>
        @endcan

        {{-- Sección Configuración --}}
        @can('ver horarios')
            <div class="pt-4 pb-1.5 px-2 flex items-center gap-2">
                <span class="text-[9.5px] font-semibold uppercase tracking-widest shrink-0"
                    style="color: #847467; letter-spacing: 0.1em;">Configuración</span>
                <div class="flex-1 h-px" style="background: #d6c3b3; opacity: 0.6;"></div>
            </div>
        @endcan

        @canany(['ver horarios', 'gestionar empresas'])
            <x-sidebar-link route="opening-hours.index" pattern="opening-hours.*" icon="calendar_month">
                Horarios de atención
            </x-sidebar-link>
        @endcanany


        @can('ver disponibilidades')
            <x-sidebar-link route="professional-availability.index" pattern="professional-availability.*"
                icon="calendar_month">
                Horarios de profesionales
            </x-sidebar-link>
        @endcan

        {{-- Sección Mis Citas --}}
        @canany(['reservar citas', 'ver historial de citas'])
            <div class="pt-4 pb-1.5 px-2 flex items-center gap-2">
                <span class="text-[9.5px] font-semibold uppercase tracking-widest shrink-0"
                    style="color: #847467; letter-spacing: 0.1em;">Mis Citas</span>
                <div class="flex-1 h-px" style="background: #d6c3b3; opacity: 0.6;"></div>
            </div>
        @endcanany

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

        {{-- Sección Reportes --}}
        @canany(['ver historial de notificaciones', 'imprimir reportes'])
            <div class="pt-4 pb-1.5 px-2 flex items-center gap-2">
                <span class="text-[9.5px] font-semibold uppercase tracking-widest shrink-0"
                    style="color: #847467; letter-spacing: 0.1em;">Reportes</span>
                <div class="flex-1 h-px" style="background: #d6c3b3; opacity: 0.6;"></div>
            </div>
        @endcanany

        @can('ver historial de notificaciones')
            <x-sidebar-link route="notification-logs.index" pattern="notification-logs.*" icon="mail">
                Notificaciones
            </x-sidebar-link>
        @endcan

        @can('imprimir reportes')
            <x-sidebar-link route="appointments.export" pattern="appointments.export*" icon="picture_as_pdf">
                Exportar Citas
            </x-sidebar-link>
        @endcan

    </nav>
</aside>

<!-- Overlay móvil -->
<div id="overlay" class="fixed inset-0 hidden md:hidden z-50 transition-all duration-300"
    style="background: rgba(28,28,25,0.45); backdrop-filter: blur(4px);">
</div>
