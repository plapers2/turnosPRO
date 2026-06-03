<x-app-layout>
    <main class="flex-1 flex flex-col bg-surface">

        <x-form.header icono="arrow_back" ruta="master" titulo="Nueva Empresa" subtitulo="Gestión de Plataforma" />

        <div class="px-8 pb-20">
            <form method="POST" action="{{ route('master.store') }}" enctype="multipart/form-data"
                class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">
                @csrf

                <!-- COLUMNA PRINCIPAL -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- CARD INFO EMPRESA -->
                    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                        <div>
                            <h2 class="text-lg font-semibold text-primary mb-1">Información de la empresa</h2>
                            <p class="text-sm text-on-surface-variant">Datos básicos del negocio</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form.field label="Nombre de la empresa" for="name">
                                <x-form.input id="name" name="name" type="text"
                                    :value="old('name')" placeholder="Ej. Salón Bella Vista"
                                    class="focus:ring-primary/10 focus:border-primary/40" />
                                <x-input-error class="mt-1" :messages="$errors->get('name')" />
                            </x-form.field>

                            <x-form.field label="Correo electrónico" for="email">
                                <x-form.input id="email" name="email" type="email"
                                    :value="old('email')" placeholder="contacto@empresa.com"
                                    class="focus:ring-primary/10 focus:border-primary/40" />
                                <x-input-error class="mt-1" :messages="$errors->get('email')" />
                            </x-form.field>

                            <x-form.field label="Teléfono" for="phone">
                                <x-form.input id="phone" name="phone" type="text"
                                    :value="old('phone')" placeholder="Ej. 3001234567"
                                    class="focus:ring-primary/10 focus:border-primary/40" />
                                <x-input-error class="mt-1" :messages="$errors->get('phone')" />
                            </x-form.field>

                            <x-form.field label="Dirección" for="address">
                                <x-form.input id="address" name="address" type="text"
                                    :value="old('address')" placeholder="Ej. Calle 10 #5-23"
                                    class="focus:ring-primary/10 focus:border-primary/40" />
                                <x-input-error class="mt-1" :messages="$errors->get('address')" />
                            </x-form.field>
                        </div>

                        <div class="md:col-span-2" x-data="{ tipoType: '{{ old('type_type', 'existing') }}' }">
                            <label class="block text-sm font-medium text-on-surface mb-2">Tipo de empresa</label>

                            {{-- Selector de modo --}}
                            <div class="flex gap-3 mb-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="type_type" value="existing" x-model="tipoType" class="sr-only" />
                                    <div :class="tipoType === 'existing'
                    ? 'border-primary bg-primary/5 text-primary'
                    : 'border-outline-variant/30 text-on-surface-variant hover:border-primary/40'"
                                        class="flex items-center gap-3 p-4 rounded-xl border-2 transition">
                                        <span class="material-symbols-outlined text-[20px]">storefront</span>
                                        <div>
                                            <p class="text-sm font-semibold">Tipo existente</p>
                                            <p class="text-xs opacity-70">Selecciona de los tipos ya creados</p>
                                        </div>
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="type_type" value="new" x-model="tipoType" class="sr-only" />
                                    <div :class="tipoType === 'new'
                    ? 'border-primary bg-primary/5 text-primary'
                    : 'border-outline-variant/30 text-on-surface-variant hover:border-primary/40'"
                                        class="flex items-center gap-3 p-4 rounded-xl border-2 transition">
                                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                                        <div>
                                            <p class="text-sm font-semibold">Crear nuevo tipo</p>
                                            <p class="text-xs opacity-70">Define un nuevo tipo de negocio</p>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            {{-- Existente --}}
                            <div x-show="tipoType === 'existing'" x-cloak>
                                <x-form.select id="type_company_id" name="type_company_id">
                                    <option value="">Selecciona un tipo...</option>
                                    @foreach ($typeCompanies as $type)
                                    <option value="{{ $type->id }}" {{ old('type_company_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                    @endforeach
                                </x-form.select>
                                <x-input-error class="mt-1" :messages="$errors->get('type_company_id')" />
                            </div>

                            {{-- Nuevo --}}
                            <div x-show="tipoType === 'new'" x-cloak>
                                <x-form.input id="type_company_name" name="type_company_name" type="text"
                                    :value="old('type_company_name')"
                                    placeholder="Ej. Barbería, Clínica dental, Spa..."
                                    class="focus:ring-primary/10 focus:border-primary/40" />
                                <x-input-error class="mt-1" :messages="$errors->get('type_company_name')" />
                            </div>
                        </div>
                    </div>

                    <!-- CARD ADMINISTRADOR -->
                    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6"
                        x-data="{ adminType: '{{ old('admin_type', 'new') }}' }">
                        <div>
                            <h2 class="text-lg font-semibold text-primary mb-1">Administrador de la empresa</h2>
                            <p class="text-sm text-on-surface-variant">Asigna quién gestionará esta empresa</p>
                        </div>

                        <div class="flex gap-3">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="admin_type" value="new" x-model="adminType" class="sr-only" />
                                <div :class="adminType === 'new'
                                        ? 'border-primary bg-primary/5 text-primary'
                                        : 'border-outline-variant/30 text-on-surface-variant hover:border-primary/40'"
                                    class="flex items-center gap-3 p-4 rounded-xl border-2 transition">
                                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                                    <div>
                                        <p class="text-sm font-semibold">Crear nuevo admin</p>
                                        <p class="text-xs opacity-70">El sistema genera credenciales y las envía por correo</p>
                                    </div>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="admin_type" value="existing" x-model="adminType" class="sr-only" />
                                <div :class="adminType === 'existing'
                                        ? 'border-primary bg-primary/5 text-primary'
                                        : 'border-outline-variant/30 text-on-surface-variant hover:border-primary/40'"
                                    class="flex items-center gap-3 p-4 rounded-xl border-2 transition">
                                    <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                                    <div>
                                        <p class="text-sm font-semibold">Admin existente</p>
                                        <p class="text-xs opacity-70">Vincula un administrador ya registrado</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div x-show="adminType === 'new'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form.field label="Nombre completo" for="admin_name">
                                <x-form.input id="admin_name" name="admin_name" type="text"
                                    :value="old('admin_name')" placeholder="Ej. Carlos Ramírez"
                                    class="focus:ring-primary/10 focus:border-primary/40" />
                                <x-input-error class="mt-1" :messages="$errors->get('admin_name')" />
                            </x-form.field>
                            <x-form.field label="Correo electrónico" for="admin_email">
                                <x-form.input id="admin_email" name="admin_email" type="email"
                                    :value="old('admin_email')" placeholder="admin@empresa.com"
                                    class="focus:ring-primary/10 focus:border-primary/40" />
                                <x-input-error class="mt-1" :messages="$errors->get('admin_email')" />
                            </x-form.field>
                            <div class="md:col-span-2">
                                <div class="flex items-start gap-2 p-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-xs">
                                    <span class="material-symbols-outlined text-[16px] mt-0.5 flex-shrink-0">info</span>
                                    Se generará una contraseña temporal y se enviará al correo del administrador.
                                    En su primer ingreso deberá cambiarla obligatoriamente.
                                </div>
                            </div>
                        </div>

                        <div x-show="adminType === 'existing'" x-cloak>
                            <x-form.field label="Seleccionar administrador" for="admin_id">
                                <x-form.select id="admin_id" name="admin_id">
                                    <option value="">Buscar administrador...</option>
                                    @foreach ($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ old('admin_id') == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }} — {{ $admin->email }}
                                    </option>
                                    @endforeach
                                </x-form.select>
                                <x-input-error class="mt-1" :messages="$errors->get('admin_id')" />
                            </x-form.field>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR -->
                <div class="space-y-8">

                    <!-- LOGO -->
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
                        <h3 class="text-sm font-semibold text-primary">Logo de la empresa</h3>

                        <div class="flex justify-center">
                            <div class="w-24 h-24 rounded-xl overflow-hidden border-2 border-outline-variant/20 bg-primary/10 flex items-center justify-center">
                                <img id="logo_preview" src="" class="w-full h-full object-cover hidden" />
                                <span id="logo_placeholder" class="material-symbols-outlined text-3xl text-primary/30">add_business</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="logo" class="text-xs font-medium text-on-surface-variant">PNG o JPG hasta 10MB</label>
                            <input type="file" id="logo" name="logo" accept="image/png,image/jpg,image/jpeg"
                                class="w-full text-sm text-on-surface-variant file:mr-3 file:py-1.5 file:px-3
                                       file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                       file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition" />
                            <x-input-error :messages="$errors->get('logo')" />
                        </div>
                    </div>

                    <!-- INFO -->
                    <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">
                        <h3 class="text-sm font-semibold text-primary">Recomendaciones</h3>
                        <ul class="text-sm text-on-surface-variant space-y-2">
                            <li>• Usa un logo claro con fondo transparente o blanco</li>
                            <li>• Verifica que el correo de la empresa sea válido</li>
                            <li>• El administrador recibirá sus credenciales por correo</li>
                        </ul>
                    </div>

                    <!-- BOTONES -->
                    <div class="flex justify-end gap-4 pt-4">
                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg text-sm font-semibold
                                   bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
                            Crear empresa
                        </button>
                        <a href="{{ route('master.index') }}"
                            class="px-5 py-2.5 rounded-lg text-sm font-semibold
                                   bg-surface-container hover:bg-surface-container-high transition">
                            Cancelar
                        </a>
                    </div>

                </div>

            </form>
        </div>

        <script>
            document.getElementById('logo').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = () => {
                    const preview = document.getElementById('logo_preview');
                    const placeholder = document.getElementById('logo_placeholder');
                    preview.src = reader.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            });
        </script>
    </main>
</x-app-layout>