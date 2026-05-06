<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <x-form.header icono="business" titulo="Editar Empresa" subtitulo="Panel de gestión" ruta="companies" />

        <div class="px-8 pb-20">
            <form method="POST" action="{{ route('companies.update', $company->id) }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PATCH')
                @include('company.form')
            </form>
        </div>

    </main>
</x-app-layout>