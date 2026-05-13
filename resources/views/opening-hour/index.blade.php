<x-app-layout>
    <main class="flex-1 flex flex-col min-h-0 overflow-y-auto bg-surface">

        <x-header-admin
            icono="schedule"
            titulo="Horarios de Atención"
            mensaje="Visualiza y administra los horarios por día"
            textoBoton="Nuevo horario"
            mensajeEmpleado="Visualiza los horarios de la empresa a la que perteneces"
            ruta="opening-hours"
        />

        @livewire('opening-hours.opening-hour-index')

    </main>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
