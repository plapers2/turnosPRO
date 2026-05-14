<x-app-layout>
    <main class="flex-1 flex flex-col bg-surface">

        <!-- HEADER -->
        <x-form.header icono="arrow_back" ruta="users" titulo="Nuevo Profesional" subtitulo="Gestion de Profesionales" />

        <!-- FORM -->
        <div class="px-8 pb-20">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data"
                class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">
                @csrf

                @include('users.form')

            </form>

        </div>

    </main>
</x-app-layout>