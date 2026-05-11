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
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                </div>
                <div class="flex flex-col gap-0.5">
                    <h2 class="text-[17px] font-semibold leading-tight tracking-tight text-on-surface">
                        Editar Empresa
                    </h2>
                    <p class="text-[13px] text-on-surface-variant">{{ $company->name }}</p>
                </div>
            </div>
            <a href="{{ route('master.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 px-4 py-2 text-[13px]
                      font-semibold text-on-surface-variant hover:bg-surface-container transition">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Volver
            </a>
        </header>

        <div class="px-8 pb-20">
            <div class="max-w-4xl space-y-6">

                <!-- FORM EMPRESA -->
                <form method="POST" action="{{ route('master.update', $company->id) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-base font-semibold text-primary">Información de la empresa</h3>
                            <p class="text-sm text-on-surface-variant mt-0.5">Nombre, tipo y datos de contacto</p>
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

                        <x-form.field label="Tipo de empresa" for="type_company_id">
                            <x-form.select id="type_company_id" name="type_company_id">
                                @foreach ($typeCompanies as $type)
                                <option value="{{ $type->id }}"
                                    {{ old('type_company_id', $company->type_company_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </x-form.select>
                            <x-input-error class="mt-1" :messages="$errors->get('type_company_id')" />
                        </x-form.field>

                        <x-form.field label="Logo" for="logo">
                            @if ($company->logo)
                            <div class="flex items-center gap-3 mb-3">
                                <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo actual"
                                    class="w-16 h-16 rounded-lg object-cover border border-outline-variant/20" />
                                <p class="text-xs text-on-surface-variant">Logo actual — sube uno nuevo para reemplazarlo</p>
                            </div>
                            @endif
                            <x-form.input-file id="logo" name="logo" accept="image/png,image/jpg,image/jpeg" />
                            <x-input-error class="mt-1" :messages="$errors->get('logo')" />
                        </x-form.field>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary
                                           text-sm font-semibold text-on-primary shadow-sm hover:opacity-90 transition">
                                <span class="material-symbols-outlined text-[16px]">save</span>
                                Guardar cambios
                            </button>
                        </div>
                    </div>
                </form>

                <!-- CARD ADMINISTRADORES ACTUALES -->
                <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-5">
                    <div>
                        <h3 class="text-base font-semibold text-primary">Administradores asignados</h3>
                        <p class="text-sm text-on-surface-variant mt-0.5">Usuarios con acceso de administrador a esta empresa</p>
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

                    <!-- Asignar admin existente -->
                    <form method="POST" action="{{ route('master.assign-admin', $company->id) }}" class="pt-2 border-t border-outline-variant/20">
                        @csrf
                        <p class="text-xs font-semibold text-on-surface-variant mb-3 uppercase tracking-wide">Asignar administrador existente</p>
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <x-form.select id="admin_id" name="admin_id">
                                    <option value="">Selecciona un administrador...</option>
                                    @foreach ($admins as $admin)
                                    <option value="{{ $admin->id }}">
                                        {{ $admin->name }} — {{ $admin->email }}
                                    </option>
                                    @endforeach
                                </x-form.select>
                                <x-input-error class="mt-1" :messages="$errors->get('admin_id')" />
                            </div>
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary
                                           text-sm font-semibold text-on-primary hover:opacity-90 transition flex-shrink-0">
                                <span class="material-symbols-outlined text-[16px]">person_add</span>
                                Asignar
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>
</x-app-layout>