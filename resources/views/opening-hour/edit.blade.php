<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-form.header icono="arrow_back" titulo="Editar horario" subtitulo="Modifica el horario seleccionado"
            ruta="opening-hours" />

        <!-- FORM -->
        <div class="px-8 pb-20">
            <form action="{{ route('opening-hours.update', $openingHour->id) }}" method="POST"
                class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">

                @csrf
                @method('PATCH')

                @include('opening-hour.form', ['mode' => 'edit'])

            </form>
        </div>

    </main>
</x-app-layout>
