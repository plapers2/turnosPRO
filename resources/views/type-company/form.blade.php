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
            <a href="{{ route('master.type-companies.index') }}"
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

        <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
            <h3 class="text-sm font-semibold text-primary">Logo del tipo de empresa</h3>

            <div class="flex justify-center">
                <div class="w-24 h-24 rounded-xl overflow-hidden border-2 border-outline-variant/20 bg-primary/10 flex items-center justify-center">
                    @if(isset($typeCompany) && $typeCompany->logo)
                    <img id="preview" src="{{ asset('storage/' . $typeCompany->logo) }}" class="w-full h-full object-cover" />
                    @else
                    <img id="preview" src="" class="w-full h-full object-cover hidden" />
                    <span id="initials" class="material-symbols-outlined text-3xl text-primary/30">category</span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="logo" class="text-xs font-medium text-on-surface-variant">PNG o JPG hasta 10MB</label>
                <input type="file" id="logo" name="logo" accept="image/png,image/jpg,image/jpeg"
                    class="w-full text-sm text-on-surface-variant file:mr-3 file:py-1.5 file:px-3
            file:rounded-lg file:border-0 file:text-xs file:font-semibold
            file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition" />
            </div>
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
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = () => {
            const preview = document.getElementById('preview');
            const initials = document.getElementById('initials');
            preview.src = reader.result;
            preview.classList.remove('hidden');
            if (initials) initials.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
</script>