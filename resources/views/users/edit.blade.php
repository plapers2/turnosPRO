<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-form.header icono="arrow_back" titulo="Editar Usuario" subtitulo="Gestion de Profesionales" />

        <!-- FORM -->
        <div class="px-8 pb-20">
            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data"
                class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">
                @csrf
                @method('PUT')
                @include('users.form')
            </form>
        </div>

    </main>
</x-app-layout>

<script>
    const input = document.getElementById('image');
    const preview = document.getElementById('preview');

    input.addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = () => {
            preview.src = reader.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });
</script>
