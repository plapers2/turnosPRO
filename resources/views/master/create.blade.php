<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <header class="relative mx-8 mt-10 mb-4 overflow-hidden rounded-2xl border border-outline-variant/30
                       bg-surface-container-lowest px-6 py-10
                       flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4
                       shadow-[0_1px_8px_rgba(95,94,90,0.06)]">
            <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-primary rounded-l-2xl"></div>
            <div class="flex items-center gap-4 pl-2">
                <div class="flex h-[42px] w-[42px] shrink-0 items-center justify-center
                            rounded-xl border border-primary-fixed-dim/40 bg-primary-fixed/20 text-primary">
                    <span class="material-symbols-outlined text-[20px]">add_business</span>
                </div>
                <div class="flex flex-col gap-0.5">
                    <h2 class="text-[17px] font-semibold leading-tight tracking-tight text-on-surface">Nueva Empresa</h2>
                    <p class="text-[13px] text-on-surface-variant">Registra una nueva empresa en la plataforma</p>
                </div>
            </div>
            <a href="{{ route('master.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 px-4 py-2 text-[13px]
                      font-semibold text-on-surface-variant hover:bg-surface-container transition">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Volver
            </a>
        </header>

        <!-- FORM -->
        <div class="px-8 pb-20">
            <form method="POST" action="{{ route('master.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="max-w-4xl space-y-6">

                    <!-- CARD EMPRESA -->
                    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-base font-semibold text-primary">Información de la empresa</h3>
                            <p class="text-sm text-on-surface-variant mt-0.5">Datos básicos del negocio</p>
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

                        <x-form.field label="Tipo de empresa" for="type_company_id">
                            <x-form.select id="type_company_id" name="type_company_id">
                                <option value="">Selecciona un tipo...</option>
                                @foreach ($typeCompanies as $type)
                                <option value="{{ $type->id }}" {{ old('type_company_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </x-form.select>
                            <x-input-error class="mt-1" :messages="$errors->get('type_company_id')" />
                        </x-form.field>

                        <x-form.field label="Logo (opcional)" for="logo">
                            <x-form.input-file id="logo" name="logo" accept="image/png,image/jpg,image/jpeg" />
                            <x-input-error class="mt-1" :messages="$errors->get('logo')" />
                        </x-form.field>
                    </div>

                    <!-- CARD ADMINISTRADOR -->
                    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6"
                        x-data="{ adminType: '{{ old('admin_type', 'new') }}' }">
                        <div>
                            <h3 class="text-base font-semibold text-primary">Administrador de la empresa</h3>
                            <p class="text-sm text-on-surface-variant mt-0.5">Asigna quién gestionará esta empresa</p>
                        </div>

                        <!-- Selector tipo -->
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

                        <!-- Nuevo admin -->
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

                        <!-- Admin existente -->
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

                    <!-- BOTONES -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('master.index') }}"
                            class="px-5 py-2.5 rounded-xl border border-outline-variant/30 text-sm font-semibold
                                  text-on-surface-variant hover:bg-surface-container transition">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary
                                       text-sm font-semibold text-on-primary shadow-sm hover:opacity-90 transition">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Crear empresa
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </main>
</x-app-layout>