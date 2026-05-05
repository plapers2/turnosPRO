<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <x-header-admin icono="group" titulo="Gestion de Profesionales" mensaje="Administra los usuarios del sistema"
            textoBoton="Nuevo Usuario" ruta="users" />

        @livewire('users.user-index')

    </main>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
