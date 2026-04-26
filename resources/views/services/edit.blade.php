<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-form.header icono="arrow_back" titulo="Editar Servicio" subtitulo="Panel de gestión" ruta="services" />

        <!-- CONTENIDO -->
        <div class="px-8 pb-20">
            <form action="{{ route('services.update', $service->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                @method('PUT')
                @include('services.form')
            </form>
        </div>

    </main>
</x-app-layout>
