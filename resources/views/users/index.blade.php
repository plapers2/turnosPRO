<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HEADER-->
        <x-header-admin icono="group" titulo="Gestion de Profesionales" mensaje="Administra los usuarios del sistema"
            textoBoton="Nuevo Usuario" ruta="users" />

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

                <div class="hidden md:block overflow-x-auto">

                    <table class="w-full text-sm">

                        <!-- THEAD -->
                        <thead class="bg-surface/50 text-on-surface-variant">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">Usuario</th>
                                <th class="px-6 py-4 text-left font-semibold">Contacto</th>
                                <th class="px-6 py-4 text-left font-semibold">Estado</th>
                                <th class="px-6 py-2 text-center font-semibold">Rol</th>
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
                                            {{ $user->deleted_at ? 'bg-error/20 text-on-error-container' : 'bg-indigo-400/20 text-indigo-700' }}">
                                            {{ $user->deleted_at ? 'Inactivo' : 'Activo' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end flex-wrap gap-2">

                                            @forelse ($user->getRoleNames() as $role)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold
                                                        @switch($role)
                                                            @case('Admin')
                                                                bg-red-100 text-red-700
                                                                @break
                                                            @case('Empleado')
                                                                bg-blue-100 text-blue-700
                                                                @break
                                                            @case('Cliente')
                                                                bg-green-100 text-green-700
                                                                @break
                                                            @default
                                                                bg-gray-100 text-gray-700
                                                        @endswitch
                                                        ">
                                                    {{ ucfirst($role) }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-400 italic">
                                                    Sin rol
                                                </span>
                                            @endforelse

                                        </div>
                                    </td>

                                    <!-- ACCIONES -->
                                    <td class="px-6 py-4 text-right">
                                        @if ($user->trashed())
                                            <!-- RESTAURAR -->
                                            <button onclick="restoreUser(this, {{ $user->id }})"
                                                class="text-green-600 hover:text-green-800 transition">
                                                Restaurar
                                            </button>
                                        @else
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
                                        @endif

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

                <div class="md:hidden space-y-4">

                    @forelse ($users as $user)
                        <div
                            class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 shadow-sm">

                            <!-- TOP -->
                            <div class="flex items-center gap-4 mb-3">
                                <img class="w-12 h-12 rounded-lg object-cover"
                                    src="{{ $user->image ? asset('storage/' . $user->image) : 'https://ui-avatars.com/api/?name=' . $user->name }}">

                                <div>
                                    <p class="font-semibold text-primary">
                                        {{ $user->name }}
                                    </p>
                                    <p class="text-xs text-on-surface-variant">
                                        ID: {{ $user->id }}
                                    </p>
                                </div>
                            </div>

                            <!-- INFO -->
                            <div class="space-y-2 text-sm">

                                <p class="text-on-surface-variant">
                                    <span class="text-secondary font-bold">Email:</span> {{ $user->email }}
                                </p>

                                <p class="text-xs text-on-surface-variant">
                                    <span class="text-secondary font-bold">Telefono:</span>
                                    {{ $user->phone ?? 'Sin teléfono' }}
                                </p>

                                <!-- ESTADO -->
                                <div>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold
                                            {{ $user->deleted_at ? 'bg-error/20 text-on-error-container' : 'bg-indigo-400/20 text-indigo-700' }}">
                                        {{ $user->deleted_at ? 'Inactivo' : 'Activo' }}
                                    </span>
                                </div>

                                <!-- ROLES -->
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @forelse ($user->getRoleNames() as $role)
                                        <span
                                            class="text-xs px-2 py-1 rounded-md
                            @switch($role)
                                @case('Admin') bg-red-100 text-red-700 @break
                                @case('Empleado') bg-blue-100 text-blue-700 @break
                                @case('Cliente') bg-green-100 text-green-700 @break
                                @default bg-gray-100 text-gray-700
                            @endswitch">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Sin rol</span>
                                    @endforelse
                                </div>

                            </div>

                            <!-- ACCIONES -->
                            <div class="flex justify-end gap-4 mt-4 border-t pt-3">
                                @if ($user->trashed())
                                    <!-- RESTAURAR -->
                                    <button onclick="restoreUser({{ $user->id }})"
                                        class="text-green-600 hover:text-green-800 transition">
                                        Restaurar
                                    </button>
                                @else
                                    <!-- EDITAR -->
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="text-primary hover:text-primary-container transition">
                                        Editar
                                    </a>

                                    <!-- SOFT DELETE -->
                                    <button onclick="deleteUser({{ $user->id }})"
                                        class="text-error hover:text-on-error-container transition">
                                        Eliminar
                                    </button>
                                @endif
                            </div>

                        </div>

                    @empty
                        <p class="text-center text-sm text-gray-400">
                            No hay usuarios
                        </p>
                    @endforelse

                </div>


                <!-- PAGINACIÓN -->
                <div class="px-6 py-4 border-t border-outline-variant/20">
                    {{ $users->links() }}
                </div>

            </div>

        </div>

    </main>
</x-app-layout>




<script>
    async function restoreUser(button, id) {

        const original = button.innerHTML;

        button.innerHTML = `
        <span class="animate-spin inline-block w-5 h-5 border-2 border-current border-t-transparent rounded-full"></span>
    `;
        button.disabled = true;

        try {
            const response = await fetch(`/users/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                location.reload();
            } else {
                throw new Error();
            }

        } catch (error) {
            button.innerHTML = original;
            button.disabled = false;
        }
    }

    function deleteUser(id) {

        Swal.fire({
            title: '¿Eliminar este usuario?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ba1a1a', // color error
            cancelButtonColor: '#847467', // outline
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            background: '#fcf9f3',
            color: '#1c1c19',
            customClass: {
                popup: 'rounded-xl shadow-lg',
                confirmButton: 'px-4 py-2 rounded-lg font-semibold',
                cancelButton: 'px-4 py-2 rounded-lg'
            },
        }).then(async (result) => {

            if (result.isConfirmed) {

                try {
                    const response = await fetch(`/users/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        Swal.fire({
                            title: 'Eliminado',
                            text: 'El usuario fue eliminado correctamente',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#fcf9f3',
                            color: '#1c1c19'
                        }).then(() => {
                            location.reload();
                        })

                    } else {
                        throw new Error();
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo eliminar: ' + error,
                        icon: 'error',
                        background: '#fcf9f3',
                        color: '#1c1c19'
                    });
                }

            }

        });
    }
</script>
