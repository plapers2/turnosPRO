<div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- FORM -->
    <div class="lg:col-span-2 space-y-8">

        <!-- CARD INFORMACIÓN BÁSICA -->
        <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-8">

            <div>
                <h2 class="text-lg font-semibold text-primary mb-1">Información básica</h2>
                <p class="text-sm text-on-surface-variant">Define los detalles del Tipo de Empresa</p>
            </div>

            <x-form.field label="Nombre del Tipo de Empresa" for="name">
                <x-form.input id="name" name="name" type="text"
                    :value="old('name', $typeCompany?->name)"
                    placeholder="Ej. Farmacia, Salón de Belleza"
                    class="focus:ring-primary/10 focus:border-primary/40" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </x-form.field>

        </div>

        <!-- BOTONES -->
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('companies.index') }}"
                class="px-5 py-2.5 rounded-lg text-sm font-semibold bg-surface-container hover:bg-surface-container-high transition">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-2.5 rounded-lg text-sm font-semibold bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
                Guardar Tipo de Empresa
            </button>
        </div>

    </div>

    <!-- SIDEBAR -->
    <div class="space-y-8">

        <div class="bg-surface-container rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
            <h3 class="text-md font-semibold text-primary">Logo de Tipo de Empresa</h3>
            <x-form.input-file name="logo" id="logo" />
            <img id="preview" src="{{ $typeCompany?->logo ? asset('storage/' . $typeCompany->logo) : '' }}"
                class="{{ isset($typeCompany) && $typeCompany->logo ? '' : 'hidden' }} w-full h-40 object-cover rounded-lg border border-outline-variant/20">
        </div>

        <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">
            <h3 class="text-sm font-semibold text-primary">Recomendaciones</h3>
            <ul class="text-sm text-on-surface-variant space-y-2">
                <li>• Usa un logo de buena resolución</li>
                <li>• Verifica que el nombre sea acorde a la imagen</li>
            </ul>
        </div>

    </div>

</div>

<script>
    const input = document.getElementById('logo');
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