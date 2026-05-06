<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <x-header-admin
            icono="category"
            titulo="Gestión de Tipos de Empresa"
            mensaje="Administra los tipos de empresa registrados en el sistema"
            textoBoton="Nuevo Tipo de Empresa"
            ruta="type-companies" />

        @livewire('companies.type-company-index')

    </main>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>