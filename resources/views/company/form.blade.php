<div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- FORM -->
    <div class="lg:col-span-2 space-y-8">

        <!-- CARD INFORMACIÓN BÁSICA -->
        <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-8">

            <div>
                <h2 class="text-lg font-semibold text-primary mb-1">Información básica</h2>
                <p class="text-sm text-on-surface-variant">Define los detalles principales de la empresa</p>
            </div>

            <x-form.field label="Nombre de la empresa" for="name">
                <x-form.input id="name" name="name" type="text"
                    :value="old('name', $company?->name)"
                    placeholder="Ej. Salón Bella Vista"
                    class="focus:ring-primary/10 focus:border-primary/40" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </x-form.field>

            <x-form.field label="Correo electrónico" for="email">
                <x-form.input id="email" name="email" type="email"
                    :value="old('email', $company?->email)"
                    placeholder="Ej. contacto@empresa.com"
                    class="focus:ring-primary/10 focus:border-primary/40" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </x-form.field>

        </div>

        <!-- CARD CONFIGURACIÓN -->
        <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

            <h2 class="text-lg font-semibold text-primary">Configuración</h2>

            <div class="grid grid-cols-2 gap-6">

                <x-form.field label="Teléfono" for="phone">
                    <x-form.input id="phone" name="phone" type="text"
                        :value="old('phone', $company?->phone)"
                        placeholder="Ej. 3001234567"
                        class="focus:ring-secondary/10 focus:border-secondary/40" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </x-form.field>

                <x-form.field label="Dirección" for="address">
                    <x-form.input id="address" name="address" type="text"
                        :value="old('address', $company?->address)"
                        placeholder="Ej. Calle 10 #5-23"
                        class="focus:ring-secondary/10 focus:border-secondary/40" />
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </x-form.field>
                <div class="col-span-2">
                    <x-form.field label="Tipo de empresa" for="type_company_id">

                        <!-- Input hidden que guarda el valor real -->
                        <input type="hidden" name="type_company_id" id="type_company_id"
                            value="{{ old('type_company_id', $company->type_company_id) }}">

                        <!-- Input visible de búsqueda -->
                        <div class="relative">
                            <input type="text" id="type_company_search"
                                placeholder="Buscar tipo de empresa..."
                                autocomplete="off"
                                class="w-full rounded-lg border border-outline-variant/40 px-3 py-2 text-sm focus:ring-primary/10 focus:border-primary/40"
                                value="{{ old('type_company_id') ? $typeCompanies->firstWhere('id', old('type_company_id'))?->name : $company?->typeCompany?->name }}" />

                            <!-- Dropdown -->
                            <ul id="type_company_dropdown"
                                class="absolute z-50 w-full bg-white border border-outline-variant/30 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto hidden">
                                @foreach ($typeCompanies as $type)
                                <li class="type-option px-3 py-2 text-sm cursor-pointer hover:bg-primary/10 transition"
                                    data-id="{{ $type->id }}"
                                    data-name="{{ $type->name }}">
                                    {{ $type->name }}
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <x-input-error class="mt-2" :messages="$errors->get('type_company_id')" />
                    </x-form.field>
                </div>


            </div>

        </div>

        <!-- BOTONES -->
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('companies.index') }}"
                class="px-5 py-2.5 rounded-lg text-sm font-semibold bg-surface-container hover:bg-surface-container-high transition">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-2.5 rounded-lg text-sm font-semibold bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
                Guardar empresa
            </button>
        </div>

    </div>

    <!-- SIDEBAR -->
    <div class="space-y-8">

        <div class="bg-surface-container rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
            <h3 class="text-md font-semibold text-primary">Logo de la empresa</h3>
            <x-form.input-file name="logo" id="logo" />
            <img id="preview" src="{{ $company?->logo ? asset('storage/' . $company->logo) : '' }}"
                class="{{ isset($company) && $company->logo ? '' : 'hidden' }} w-full h-40 object-cover rounded-lg border border-outline-variant/20">
        </div>

        <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">
            <h3 class="text-sm font-semibold text-primary">Recomendaciones</h3>
            <ul class="text-sm text-on-surface-variant space-y-2">
                <li>• Usa un logo de buena resolución</li>
                <li>• El correo debe ser válido y activo</li>
                <li>• Verifica la dirección antes de guardar</li>
            </ul>
        </div>

        <div class="bg-tertiary-container/10 rounded-xl p-6 border border-tertiary/10">
            <p class="text-sm text-tertiary font-medium">
                La empresa será visible para los clientes al instante
            </p>
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
<script>
    const searchInput = document.getElementById('type_company_search');
    const hiddenInput = document.getElementById('type_company_id');
    const dropdown = document.getElementById('type_company_dropdown');
    const options = document.querySelectorAll('.type-option');

    // Mostrar dropdown al escribir
    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        let hasResults = false;

        options.forEach(opt => {
            const match = opt.dataset.name.toLowerCase().includes(query);
            opt.style.display = match ? 'block' : 'none';
            if (match) hasResults = true;
        });

        dropdown.classList.toggle('hidden', !hasResults && query === '');
        if (query === '') hiddenInput.value = '';
        dropdown.classList.remove('hidden');
    });

    // Seleccionar opción
    options.forEach(opt => {
        opt.addEventListener('click', () => {
            hiddenInput.value = opt.dataset.id;
            searchInput.value = opt.dataset.name;
            dropdown.classList.add('hidden');
        });
    });

    // Cerrar dropdown al hacer click fuera
    document.addEventListener('click', e => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Abrir dropdown al hacer foco
    searchInput.addEventListener('focus', () => {
        options.forEach(opt => opt.style.display = 'block');
        dropdown.classList.remove('hidden');
    });
</script>