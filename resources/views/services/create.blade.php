<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <header class="px-8 mt-10 mb-6 flex items-center justify-between">

            <div class="flex items-center gap-4">
                <a href="{{ route('services.index') }}"
                    class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Volver
                </a>

                <div class="h-6 w-px bg-outline-variant/40"></div>

                <h1 class="text-2xl font-bold text-primary tracking-tight">
                    Nuevo Servicio
                </h1>
            </div>

            <div class="text-sm text-on-surface-variant">
                Panel de gestión
            </div>

        </header>

        <!-- CONTENIDO -->
        <div class="px-8 pb-20">
            <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @include('services.form')
            </form>
        </div>

    </main>
</x-app-layout>
