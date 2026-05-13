<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <x-header-admin  icono="schedule" titulo="Disponibilidad Profesional"
            mensaje="Visualiza los horarios de cada empleado por dia" mensajeEmpleado="Mis horarios de atención" />

        @livewire('professional-availability.professional-availability-index')

    </main>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
