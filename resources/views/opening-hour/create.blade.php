<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-form.header icono="arrow_back" titulo="Nuevo horario" ruta="opening-hours" subtitulo="Configuración de atención" />

        <!-- FORM -->
        <div class="px-8 pb-20">
            <form action="{{ route('opening-hours.store') }}" method="POST"
                class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">
                @csrf

                @include('opening-hour.form')

            </form>
        </div>

    </main>
</x-app-layout>
