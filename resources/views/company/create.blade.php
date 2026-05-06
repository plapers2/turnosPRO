<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-form.header icono="business" titulo="Nueva Empresa" subtitulo="Panel de gestión" ruta="companies" />

        <!-- CONTENIDO -->
        <div class="px-8 pb-20">
            <form method="POST" action="{{ route('companies.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @include('company.form')
            </form>
        </div>

    </main>
</x-app-layout>