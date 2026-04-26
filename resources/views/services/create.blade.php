<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-form.header icono="arrow_back" titulo="Nuevo Servicio" subtitulo="Panel de gestión" ruta="services" />

        <!-- CONTENIDO -->
        <div class="px-8 pb-20">
            <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @include('services.form')
            </form>
        </div>

    </main>
</x-app-layout>
