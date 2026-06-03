<x-app-layout>
    <main class="flex-1 flex flex-col bg-surface">

        <x-form.header icono="arrow_back" ruta="master" titulo="Editar Empresa" subtitulo="{{ $company->name }}" />

        <div class="px-8 pb-20">
            <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- COLUMNA PRINCIPAL -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- FORM EMPRESA -->
                    <form method="POST" action="{{ route('master.update', $company->id) }}" enctype="multipart/form-data"
                        id="form-empresa">
                        @csrf @method('PUT')

                        <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-primary mb-1">Información de la empresa</h2>
                                <p class="text-sm text-on-surface-variant">Nombre, tipo y datos de contacto</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.field label="Nombre de la empresa" for="name">
                                    <x-form.input id="name" name="name" type="text"
                                        :value="old('name', $company->name)" placeholder="Ej. Salón Bella Vista"
                                        class="focus:ring-primary/10 focus:border-primary/40" />
                                    <x-input-error class="mt-1" :messages="$errors->get('name')" />
                                </x-form.field>

                                <x-form.field label="Correo electrónico" for="email">
                                    <x-form.input id="email" name="email" type="email"
                                        :value="old('email', $company->email)" placeholder="contacto@empresa.com"
                                        class="focus:ring-primary/10 focus:border-primary/40" />
                                    <x-input-error class="mt-1" :messages="$errors->get('email')" />
                                </x-form.field>

                                <x-form.field label="Teléfono" for="phone">
                                    <x-form.input id="phone" name="phone" type="text"
                                        :value="old('phone', $company->phone)" placeholder="Ej. 3001234567"
                                        class="focus:ring-primary/10 focus:border-primary/40" />
                                    <x-input-error class="mt-1" :messages="$errors->get('phone')" />
                                </x-form.field>

                                <x-form.field label="Dirección" for="address">
                                    <x-form.input id="address" name="address" type="text"
                                        :value="old('address', $company->address)" placeholder="Ej. Calle 10 #5-23"
                                        class="focus:ring-primary/10 focus:border-primary/40" />
                                    <x-input-error class="mt-1" :messages="$errors->get('address')" />
                                </x-form.field>
                            </div>

                            <div class="md:col-span-2" x-data="{ tipoType: '{{ old('type_type', 'existing') }}' }">
                                <label class="block text-sm font-medium text-on-surface mb-2">Tipo de empresa</label>

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

                                <div x-show="tipoType === 'existing'" x-cloak>
                                    <x-form.select id="type_company_id" name="type_company_id">
                                        @foreach ($typeCompanies as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('type_company_id', $company->type_company_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                        @endforeach
                                    </x-form.select>
                                    <x-input-error class="mt-1" :messages="$errors->get('type_company_id')" />
                                </div>

                                <div x-show="tipoType === 'new'" x-cloak>
                                    <x-form.input id="type_company_name" name="type_company_name" type="text"
                                        :value="old('type_company_name')"
                                        placeholder="Ej. Barbería, Clínica dental, Spa..."
                                        class="focus:ring-primary/10 focus:border-primary/40" />
                                    <x-input-error class="mt-1" :messages="$errors->get('type_company_name')" />
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- CARD ADMINISTRADORES -->
                    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-5">
                        <div>
                            <h2 class="text-lg font-semibold text-primary mb-1">Administradores asignados</h2>
                            <p class="text-sm text-on-surface-variant">Usuarios con acceso de administrador a esta empresa</p>
                        </div>

                        @forelse ($company->users as $admin)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-surface-container/50 border border-outline-variant/20">
                            <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 bg-primary/10 flex items-center justify-center border border-outline-variant/20">
                                @if ($admin->image)
                                <img src="{{ asset('storage/' . $admin->image) }}" alt="{{ $admin->name }}" class="w-full h-full object-cover" />
                                @else
                                <span class="text-xs font-bold text-primary/50">{{ strtoupper(substr($admin->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-on-surface truncate">{{ $admin->name }}</p>
                                <p class="text-xs text-on-surface-variant truncate">{{ $admin->email }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-on-surface-variant/60 italic">Sin administradores asignados</p>
                        @endforelse

                        <form method="POST" action="{{ route('master.assign-admin', $company->id) }}"
                            class="pt-2 border-t border-outline-variant/20">
                            @csrf
                            <p class="text-xs font-semibold text-on-surface-variant mb-3 uppercase tracking-wide">
                                Asignar administrador existente
                            </p>
                            <div class="flex gap-3">
                                <div class="flex-1">
                                    @if($admins->isEmpty())
                                    <p class="text-sm text-gray-500 italic mt-2">
                                        No hay administradores disponibles.
                                        <a href="{{ route('master.create') }}" class="text-amber-700 underline font-medium">
                                            Crea uno nuevo
                                        </a>
                                    </p>
                                    @else
                                    <x-form.select id="admin_id" name="admin_id">
                                        <option value="">Selecciona un administrador...</option>
                                        @foreach ($admins as $admin)
                                        <option value="{{ $admin->id }}">
                                            {{ $admin->name }} — {{ $admin->email }}
                                        </option>
                                        @endforeach
                                    </x-form.select>
                                    <x-input-error class="mt-1" :messages="$errors->get('admin_id')" />
                                    @endif
                                </div>

                                @unless($admins->isEmpty())
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary
                   text-sm font-semibold text-on-primary hover:opacity-90 transition flex-shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                                    Asignar
                                </button>
                                @endunless
                            </div>
                        </form>
                    </div>

                </div>

                <!-- SIDEBAR -->
                <div class="space-y-8">

                    <!-- LOGO -->
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
                        <h3 class="text-sm font-semibold text-primary">Logo de la empresa</h3>

                        <div class="flex justify-center">
                            <div class="w-24 h-24 rounded-xl overflow-hidden border-2 border-outline-variant/20 bg-primary/10 flex items-center justify-center">
                                @if ($company->logo)
                                <img id="logo_preview" src="{{ asset('storage/' . $company->logo) }}" class="w-full h-full object-cover" />
                                <span id="logo_placeholder" class="material-symbols-outlined text-3xl text-primary/30 hidden">add_business</span>
                                @else
                                <img id="logo_preview" src="" class="w-full h-full object-cover hidden" />
                                <span id="logo_placeholder" class="material-symbols-outlined text-3xl text-primary/30">add_business</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="logo" class="text-xs font-medium text-on-surface-variant">PNG o JPG hasta 10MB</label>
                            <input type="file" id="logo" name="logo" accept="image/png,image/jpg,image/jpeg"
                                form="form-empresa"
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
                            <li>• Los cambios se aplican de inmediato</li>
                        </ul>
                    </div>

                    <!-- BOTONES -->
                    <div class="flex justify-end gap-4 pt-4">
                        <button type="submit" form="form-empresa"
                            class="px-6 py-2.5 rounded-lg text-sm font-semibold
                                   bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
                            Guardar cambios
                        </button>
                        <a href="{{ route('master.index') }}"
                            class="px-5 py-2.5 rounded-lg text-sm font-semibold
                                   bg-surface-container hover:bg-surface-container-high transition">
                            Cancelar
                        </a>
                    </div>

                </div>

            </div>
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