<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <x-form.header icono="category" titulo="Nuevo Tipo de Empresa" subtitulo="Panel de gestión" ruta="master.type-companies" />

        <div class="px-8 pb-20">
            <form method="POST" action="{{ route('master.type-companies.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @include('type-company.form')
            </form>
        </div>

    </main>
</x-app-layout>