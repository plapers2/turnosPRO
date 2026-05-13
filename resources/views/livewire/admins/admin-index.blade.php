<div>
    {{-- LOADING BAR --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- FILTROS --}}
    <div class="px-8 py-4 flex flex-col sm:flex-row gap-4">

        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">
                search
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar administrador..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-outline-variant
                       bg-surface-container-lowest text-on-surface placeholder:text-on-surface-variant/60
                       text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition" />
        </div>

        <select wire:model.live="status"
            class="px-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-lowest
                   text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
            <option value="">Todos los estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
        </select>

    </div>

    {{-- TABLA --}}
    <div class="px-8 pb-20">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
                    shadow-[0_10px_30px_rgba(95,94,90,0.05)] overflow-hidden">

            <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide">
                    Lista de administradores
                </h3>
                <a href="{{ route('master.admins.create') }}"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium">
                    Nuevo administrador
                </a>
            </div>

            {{-- DESKTOP --}}
            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
                wire:target="search, status" class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface/50 text-on-surface-variant">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">Administrador</th>
                            <th class="px-6 py-4 text-left font-semibold">Teléfono</th>
                            <th class="px-6 py-4 text-left font-semibold">Empresas asignadas</th>
                            <th class="px-6 py-4 text-left font-semibold">Estado</th>
                            <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @forelse ($admins as $admin)
                        <tr class="hover:bg-surface/40 transition">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    @if ($admin->image)
                                    <img class="w-10 h-10 rounded-lg object-cover"
                                        src="{{ asset('storage/' . $admin->image) }}"
                                        alt="{{ $admin->name }}">
                                    @else
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0"
                                        style="background: #ffdcbe; color: #663a00;">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-primary">{{ $admin->name }}</p>
                                        <p class="text-xs text-on-surface-variant">{{ $admin->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-on-surface-variant">
                                {{ $admin->phone ?: '—' }}
                            </td>

                            <td class="px-6 py-4">
                                @php $companies = $admin->companies ?? collect(); @endphp
                                @if ($companies->isEmpty())
                                <span class="text-xs text-on-surface-variant italic">Sin empresa</span>
                                @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($companies->take(2) as $company)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-primary/10 text-primary">
                                        {{ $company->name }}
                                    </span>
                                    @endforeach
                                    @if ($companies->count() > 2)
                                    <span class="text-xs text-on-surface-variant">+{{ $companies->count() - 2 }}</span>
                                    @endif
                                </div>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold
                                    {{ $admin->deleted_at ? 'bg-error/20 text-on-error-container' : 'bg-indigo-400/20 text-indigo-700' }}">
                                    {{ $admin->deleted_at ? 'Inactivo' : 'Activo' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if ($admin->trashed())
                                <button wire:click="restoreAdmin({{ $admin->id }})"
                                    wire:loading.attr="disabled"
                                    class="text-sm font-semibold text-green-600 hover:text-green-800 transition">
                                    <span wire:loading.remove wire:target="restoreAdmin({{ $admin->id }})">Restaurar</span>
                                    <span wire:loading wire:target="restoreAdmin({{ $admin->id }})">
                                        <span class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full"></span>
                                    </span>
                                </button>
                                @else
                                <button wire:click="confirmDelete({{ $admin->id }})"
                                    class="text-error hover:text-on-error-container transition text-sm font-semibold">
                                    Desactivar
                                </button>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-16">
                                <div class="flex flex-col items-center gap-4">
                                    <span class="material-symbols-outlined text-4xl text-primary">manage_accounts</span>
                                    <p class="text-on-surface-variant">No se encontraron administradores</p>
                                    <a href="{{ route('master.admins.create') }}"
                                        class="px-4 py-2 rounded-lg bg-primary text-white text-sm">
                                        Crear administrador
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE --}}
            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
                wire:target="search, status" class="md:hidden space-y-4 p-4">
                @forelse ($admins as $admin)
                <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center gap-4 mb-3">
                        @if ($admin->image)
                        <img class="w-10 h-10 rounded-lg object-cover"
                            src="{{ asset('storage/' . $admin->image) }}"
                            alt="{{ $admin->name }}">
                        @else
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0"
                            style="background: #ffdcbe; color: #663a00;">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        @endif
                        <div>
                            <p class="font-semibold text-primary">{{ $admin->name }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $admin->email }}</p>
                        </div>
                    </div>
                    <div class="space-y-1 text-sm mb-3">
                        <p class="text-on-surface-variant">
                            <span class="font-semibold">Teléfono:</span> {{ $admin->phone ?: '—' }}
                        </p>
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold
                            {{ $admin->deleted_at ? 'bg-error/20 text-on-error-container' : 'bg-indigo-400/20 text-indigo-700' }}">
                            {{ $admin->deleted_at ? 'Inactivo' : 'Activo' }}
                        </span>
                    </div>
                    <div class="flex justify-end gap-4 border-t pt-3">
                        @if ($admin->trashed())
                        <button wire:click="restoreAdmin({{ $admin->id }})"
                            class="text-sm font-semibold text-green-600 hover:text-green-800 transition">
                            Restaurar
                        </button>
                        @else
                        <button wire:click="confirmDelete({{ $admin->id }})"
                            class="text-error hover:text-on-error-container transition text-sm font-semibold">
                            Desactivar
                        </button>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-center text-sm text-gray-400">No hay administradores registrados</p>
                @endforelse
            </div>

            {{-- PAGINACIÓN --}}
            <div class="px-6 py-4 border-t border-outline-variant/20">
                {{ $admins->links() }}
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('confirm-delete', ({
            id
        }) => {
            Swal.fire({
                title: '¿Desactivar este administrador?',
                text: 'Podrás reactivarlo después desde esta misma pantalla',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ba1a1a',
                cancelButtonColor: '#847467',
                confirmButtonText: 'Sí, desactivar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                background: '#fcf9f3',
                color: '#1c1c19',
                customClass: {
                    popup: 'rounded-xl shadow-lg',
                    confirmButton: 'px-4 py-2 rounded-lg font-semibold',
                    cancelButton: 'px-4 py-2 rounded-lg'
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteAdmin(id);

                    $wire.on('admin-deleted', () => {
                        Swal.fire({
                            title: 'Desactivado',
                            text: 'El administrador fue desactivado correctamente',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#fcf9f3',
                            color: '#1c1c19'
                        });
                    });
                }
            });
        });
    </script>
    @endscript
</div>