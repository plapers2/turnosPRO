<!-- SideNavBar -->
<aside id="sidebar"
    class="fixed top-0 left-0 h-screen w-64 z-[60]
           transform -translate-x-full md:translate-x-0
           transition-transform duration-300 ease-in-out
           flex flex-col overflow-hidden"
    style="background: linear-gradient(180deg, #f6f3ee 0%, #f1ede8 100%); border-right: 1px solid #d6c3b3;">

    <!-- Brand Header -->
    <div class="flex flex-col items-center justify-center px-4 pt-3 pb-2 shrink-0">

        <div class="relative group flex flex-col items-center gap-1">
            <!-- Halo -->
            <div class="absolute -inset-1 rounded-xl opacity-0 blur-md transition-all duration-500 group-hover:opacity-20"
                style="background: radial-gradient(ellipse, #a0714f 0%, transparent 70%);">
            </div>

            <!-- Logo -->
            <div class="relative flex items-center justify-center transition-all duration-300 group-hover:scale-105">
                <img src="{{ asset('turnos-pro.png') }}" alt="{{ config('app.name') }}" class="h-32 w-auto object-contain"
                    style="filter: drop-shadow(0 2px 8px rgba(102,58,0,0.18));" />
            </div>
        </div>

    </div>
    <!-- Navigation Links -->
    <nav
        class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5
                scrollbar-thin scrollbar-thumb-outline-variant scrollbar-track-transparent">

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

        <!-- Separador visual para sección Empresa -->
        @canany(['gestionar empresas', 'ver horarios'])
            <div class="pt-3 pb-1 px-2">
                <span class="text-[10px] font-semibold uppercase tracking-widest"
                    style="color: #847467; letter-spacing: 0.12em;">
                    Configuración
                </span>
            </div>
        @endcanany

        @canany(['ver empresas', 'gestionar empresas'])
            <x-sidebar-link route="companies.index" pattern="companies.*" icon="business">
                Empresa
            </x-sidebar-link>
        @endcan

        @can('gestionar empresas')
            <x-sidebar-link route="type-companies.index" pattern="type-companies.*" icon="add_business">
                Tipos de Empresa
            </x-sidebar-link>
        @endcan

        @canany(['ver horarios', 'gestionar empresas'])
            <x-sidebar-link route="opening-hours.index" pattern="opening-hours.*" icon="calendar_month">
                Horarios de atención
            </x-sidebar-link>
        @endcanany

        @canany(['reservar citas', 'ver historial de citas'])
            <div class="pt-3 pb-1 px-2">
                <span class="text-[10px] font-semibold uppercase tracking-widest"
                    style="color: #847467; letter-spacing: 0.12em;">
                    Mis Citas
                </span>
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

        @canany(['ver historial de notificaciones', 'imprimir reportes'])
            <div class="pt-3 pb-1 px-2">
                <span class="text-[10px] font-semibold uppercase tracking-widest"
                    style="color: #847467; letter-spacing: 0.12em;">
                    Reportes
                </span>
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

    <!-- Footer decorativo -->
    <div class="shrink-0 px-4 py-3" style="border-top: 1px solid #e8e0d8;">
        <p class="text-[10px] text-center" style="color: #b8a89a;">
            © {{ date('Y') }} · Turnos Pro
        </p>
    </div>
</aside>

<!-- Overlay -->
<div id="overlay" class="fixed inset-0 hidden md:hidden z-50 transition-all duration-300"
    style="background: rgba(28,28,25,0.45); backdrop-filter: blur(4px);">
</div>
