<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HEADER-->
        <x-header-admin
        icono="group"
        titulo="Gestion de Profesionales"
        mensaje="Administra los usuarios del sistema"
        textoBoton="Nuevo Usuario" />

        <!-- TABLA -->
        <div class="px-8 pb-20">

            <div
                class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
               shadow-[0_10px_30px_rgba(95,94,90,0.05)] overflow-hidden">

                <!-- HEADER TABLA -->
                <div class="px-6 py-4 border-b border-outline-variant/20">
                    <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide">
                        Lista de usuarios
                    </h3>
                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <!-- THEAD -->
                        <thead class="bg-surface/50 text-on-surface-variant">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">Usuario</th>
                                <th class="px-6 py-4 text-left font-semibold">Contacto</th>
                                <th class="px-6 py-4 text-left font-semibold">Estado</th>
                                <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                            </tr>
                        </thead>

                        <!-- TBODY -->
                        <tbody class="divide-y divide-outline-variant/10">

                            @forelse ($users as $user)
                                <tr class="hover:bg-surface/40 transition">

                                    <!-- USER -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">

                                            <img class="w-12 h-12 rounded-lg object-cover"
                                                src="{{ $user->image ? asset('storage/' . $user->image) : 'https://ui-avatars.com/api/?name=' . $user->name }}"
                                                alt="{{ $user->name }}">

                                            <div>
                                                <p class="font-semibold text-primary">
                                                    {{ $user->name }}
                                                </p>
                                                <p class="text-xs text-on-surface-variant">
                                                    ID: {{ $user->id }}
                                                </p>
                                            </div>

                                        </div>
                                    </td>

                                    <!-- CONTACTO -->
                                    <td class="px-6 py-4">
                                        <p class="text-on-surface-variant">{{ $user->email }}</p>
                                        <p class="text-xs text-on-surface-variant">
                                            {{ $user->phone ?? 'Sin teléfono' }}
                                        </p>
                                    </td>

                                    <!-- ESTADO -->
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold
                                           bg-secondary-fixed text-on-secondary-fixed">
                                            Activo
                                        </span>
                                    </td>

                                    <!-- ACCIONES -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-3">

                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="text-primary hover:text-primary-container transition">
                                                Editar
                                            </a>

                                            <button onclick="deleteUser({{ $user->id }})"
                                                class="text-error hover:text-on-error-container transition">
                                                Eliminar
                                            </button>

                                        </div>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center py-16">

                                        <div class="flex flex-col items-center gap-4">
                                            <span class="material-symbols-outlined text-4xl text-primary">
                                                group
                                            </span>

                                            <p class="text-on-surface-variant">
                                                No hay usuarios registrados
                                            </p>

                                            <a href="{{ route('users.create') }}"
                                                class="px-4 py-2 rounded-lg bg-primary text-white text-sm">
                                                Crear usuario
                                            </a>
                                        </div>

                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                <!-- PAGINACIÓN -->
                <div class="px-6 py-4 border-t border-outline-variant/20">
                    {{ $users->links() }}
                </div>

            </div>

        </div>

    </main>
</x-app-layout>
