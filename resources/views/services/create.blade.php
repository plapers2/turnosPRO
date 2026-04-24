<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <!-- HEADER MEJORADO -->
        <header class="px-8 mt-10 mb-6 flex items-center justify-between">

            <div class="flex items-center gap-4">
                <a href="{{ route('services.index') }}"
                    class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Volver
                </a>

                <div class="h-6 w-px bg-outline-variant/40"></div>

                <h1 class="text-2xl font-bold text-primary tracking-tight">
                    Crear servicio
                </h1>
            </div>

            <div class="text-sm text-on-surface-variant">
                Panel de gestión
            </div>

        </header>

        <!-- CONTENIDO -->
        <div class="px-8 pb-20">

            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- FORM -->
                <div class="lg:col-span-2 space-y-8">

                    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-8">
                        @csrf

                        <!-- CARD PRINCIPAL -->
                        <div
                            class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-8">

                            <div>
                                <h2 class="text-lg font-semibold text-primary mb-1">
                                    Información básica
                                </h2>
                                <p class="text-sm text-on-surface-variant">
                                    Define los detalles principales del servicio
                                </p>
                            </div>

                            <!-- Nombre -->
                            <x-form.field label="Nombre del servicio" for="name">
                                <x-form.input value="{{ old('name') }}" type="text" id="name"
                                    placeholder="Ej. Manicura Spa"
                                    class="focus:ring-primary/10 focus:border-primary/40" />
                            </x-form.field>

                            <!-- Descripción -->
                            <x-form.field label="Descripción" for="description">
                                <x-form.textarea id="description" placeholder="Describe beneficios, proceso, etc..." />
                            </x-form.field>

                        </div>

                        <!-- CARD DETALLES -->
                        <div
                            class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

                            <h2 class="text-lg font-semibold text-primary">
                                Configuración
                            </h2>

                            <div class="grid grid-cols-2 gap-6">

                                <x-form.field label="Duración (min)" for="duration">
                                    <x-form.input value="{{ old('duration') }}" type="number" id="duration"
                                        class="focus:ring-secondary/10 focus:border-secondary/40" />
                                </x-form.field>

                                <x-form.field label="Precio (COP)" for="price">
                                    <x-form.input value="{{ old('price') }}" type="number" id="price"
                                        class="focus:ring-tertiary/10 focus:border-tertiary/40" />
                                </x-form.field>

                            </div>

                        </div>

                        <!-- BOTONES -->
                        <div class="flex justify-end gap-4 pt-4">

                            <a href="{{ route('services.index') }}"
                                class="px-5 py-2.5 rounded-lg text-sm font-semibold bg-surface-container hover:bg-surface-container-high transition">
                                Cancelar
                            </a>

                            <button type="submit"
                                class="px-6 py-2.5 rounded-lg text-sm font-semibold bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
                                Guardar servicio
                            </button>

                        </div>

                    </form>

                </div>

                <!-- SIDEBAR PRO -->
                <div class="space-y-8">

                    <!-- IMAGEN -->
                    <div
                        class="bg-surface-container rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">

                        <h3 class="text-md font-semibold text-primary">
                            Imagen del servicio
                        </h3>

                        <x-form.input-file id="image" />

                        <img id="preview"
                            class="hidden w-full h-40 object-cover rounded-lg border border-outline-variant/20">

                    </div>

                    <!-- CARD INFO -->
                    <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">

                        <h3 class="text-sm font-semibold text-primary">
                            Recomendaciones
                        </h3>

                        <ul class="text-sm text-on-surface-variant space-y-2">
                            <li>• Usa imágenes reales del servicio</li>
                            <li>• Evita descripciones genéricas</li>
                            <li>• Ajusta el precio al mercado local</li>
                        </ul>

                    </div>

                    <!-- STATUS VISUAL -->
                    <div class="bg-tertiary-container/10 rounded-xl p-6 border border-tertiary/10">

                        <p class="text-sm text-tertiary font-medium">
                            Este servicio será visible inmediatamente después de guardarlo
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </main>
</x-app-layout>

<script>
    const input = document.getElementById('image');
    const preview = document.getElementById('preview');
    const placeholder = document.getElementById('placeholder');

    input.addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = () => {
            preview.src = reader.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
</script>
