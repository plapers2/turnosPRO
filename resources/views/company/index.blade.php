<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <x-header-admin icono="business" titulo="Gestión de Empresas"
            mensaje="Administra las empresas registradas en el sistema"
            textoBoton="Nueva Empresa" ruta="companies" />

        @livewire('companies.company-index')

    </main>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>