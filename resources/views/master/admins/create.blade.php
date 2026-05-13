<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <x-form.header icono="arrow_back" ruta="master.admins" titulo="Nuevo Administrador" subtitulo="Gestión de Plataforma" />

        <div class="px-8 pb-20">
            <form method="POST" action="{{ route('master.admins.store') }}"
                class="max-w-2xl mx-auto space-y-8">
                @csrf

                <!-- CARD INFO ADMIN -->
                <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-primary mb-1">Datos del administrador</h2>
                        <p class="text-sm text-on-surface-variant">Se generará una contraseña temporal y se enviará al correo ingresado</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form.field label="Nombre completo" for="name">
                            <x-form.input id="name" name="name" type="text"
                                :value="old('name')" placeholder="Ej. Carlos Ramírez"
                                class="focus:ring-primary/10 focus:border-primary/40" />
                            <x-input-error class="mt-1" :messages="$errors->get('name')" />
                        </x-form.field>

                        <x-form.field label="Correo electrónico" for="email">
                            <x-form.input id="email" name="email" type="email"
                                :value="old('email')" placeholder="admin@empresa.com"
                                class="focus:ring-primary/10 focus:border-primary/40" />
                            <x-input-error class="mt-1" :messages="$errors->get('email')" />
                        </x-form.field>

                        <x-form.field label="Teléfono" for="phone">
                            <x-form.input id="phone" name="phone" type="number"
                                :value="old('phone')" placeholder="Ej. 3001234567"
                                class="focus:ring-primary/10 focus:border-primary/40" />
                            <x-input-error class="mt-1" :messages="$errors->get('phone')" />
                        </x-form.field>
                    </div>

                    <!-- Nota informativa -->
                    <div class="flex items-start gap-2 p-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-xs">
                        <span class="material-symbols-outlined text-[16px] mt-0.5 flex-shrink-0">info</span>
                        Se generará una contraseña temporal y se enviará al correo del administrador.
                        En su primer ingreso deberá cambiarla obligatoriamente.
                        La foto de perfil podrá agregarla desde su perfil una vez acceda al sistema.
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="flex justify-end gap-4">
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg text-sm font-semibold
                               bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
                        Crear administrador
                    </button>
                    <a href="{{ route('master.admins.index') }}"
                        class="px-5 py-2.5 rounded-lg text-sm font-semibold
                               bg-surface-container hover:bg-surface-container-high transition">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>

    </main>
</x-app-layout>