<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <x-header-admin icono="groups" titulo="Historial de Clientes"
            mensaje="Fidelidad y preferencias de tus clientes" />

        @livewire('customers.customer-index')

    </main>
</x-app-layout>