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
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar tipo de empresa..."
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

            <div class="px-6 py-4 border-b border-outline-variant/20">
                <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide">
                    Lista de tipos de empresa
                </h3>
            </div>

            {{-- DESKTOP --}}
            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
                wire:target="search, status" class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface/50 text-on-surface-variant">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">Nombre</th>
                            <th class="px-6 py-4 text-left font-semibold">Estado</th>
                            <th class="px-6 py-4 text-center font-semibold">Empresas asociadas</th>
                            <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @forelse ($typeCompanies as $typeCompany)
                        <tr class="hover:bg-surface/40 transition">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <img class="w-12 h-12 rounded-lg object-cover"
                                        src="{{ $typeCompany->logo ? asset('storage/' . $typeCompany->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($typeCompany->name) }}"
                                        alt="{{ $typeCompany->name }}">
                                    <div>
                                        <p class="font-semibold text-primary">{{ $typeCompany->name }}</p>
                                        <p class="text-xs text-on-surface-variant">ID: {{ $typeCompany->id }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold
                                    {{ $typeCompany->deleted_at ? 'bg-error/20 text-on-error-container' : 'bg-indigo-400/20 text-indigo-700' }}">
                                    {{ $typeCompany->deleted_at ? 'Inactivo' : 'Activo' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-on-surface-variant">
                                    {{ $typeCompany->companies_count ?? 0 }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if ($typeCompany->trashed())
                                <button wire:click="restoreTypeCompany({{ $typeCompany->id }})"
                                    wire:loading.attr="disabled"
                                    class="text-sm font-semibold text-green-600 hover:text-green-800 transition">
                                    <span wire:loading.remove wire:target="restoreTypeCompany({{ $typeCompany->id }})">Restaurar</span>
                                    <span wire:loading wire:target="restoreTypeCompany({{ $typeCompany->id }})">
                                        <span class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full"></span>
                                    </span>
                                </button>
                                @else
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('type-companies.edit', $typeCompany->id) }}"
                                        class="text-primary hover:text-primary-container transition">Editar</a>
                                    <button wire:click="confirmDelete({{ $typeCompany->id }})"
                                        class="text-error hover:text-on-error-container transition">Eliminar</button>
                                </div>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-16">
                                <div class="flex flex-col items-center gap-4">
                                    <span class="material-symbols-outlined text-4xl text-primary">category</span>
                                    <p class="text-on-surface-variant">No se encontraron tipos de empresa</p>
                                    <a href="{{ route('type-companies.create') }}"
                                        class="px-4 py-2 rounded-lg bg-primary text-white text-sm">
                                        Crear tipo de empresa
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
                @forelse ($typeCompanies as $typeCompany)
                <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center gap-4 mb-3">
                        <img class="w-12 h-12 rounded-lg object-cover"
                            src="{{ $typeCompany->logo ? asset('storage/' . $typeCompany->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($typeCompany->name) }}"
                            alt="{{ $typeCompany->name }}">
                        <div>
                            <p class="font-semibold text-primary">{{ $typeCompany->name }}</p>
                            <p class="text-xs text-on-surface-variant">ID: {{ $typeCompany->id }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold
                            {{ $typeCompany->deleted_at ? 'bg-error/20 text-on-error-container' : 'bg-indigo-400/20 text-indigo-700' }}">
                            {{ $typeCompany->deleted_at ? 'Inactivo' : 'Activo' }}
                        </span>
                        <p class="text-on-surface-variant">
                            <span class="text-secondary font-bold">Empresas asociadas:</span>
                            {{ $typeCompany->companies_count ?? 0 }}
                        </p>
                    </div>
                    <div class="flex justify-end gap-4 mt-4 border-t pt-3">
                        @if ($typeCompany->trashed())
                        <button wire:click="restoreTypeCompany({{ $typeCompany->id }})"
                            class="text-sm font-semibold text-green-600 hover:text-green-800 transition">
                            Restaurar
                        </button>
                        @else
                        <a href="{{ route('type-companies.edit', $typeCompany->id) }}"
                            class="text-primary hover:text-primary-container transition text-sm">Editar</a>
                        <button wire:click="confirmDelete({{ $typeCompany->id }})"
                            class="text-error hover:text-on-error-container transition text-sm">Eliminar</button>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-center text-sm text-gray-400">No hay tipos de empresa registrados</p>
                @endforelse
            </div>

            {{-- PAGINACIÓN --}}
            <div class="px-6 py-4 border-t border-outline-variant/20">
                {{ $typeCompanies->links() }}
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('confirm-delete', ({
            id
        }) => {
            Swal.fire({
                title: '¿Eliminar este tipo de empresa?',
                text: 'Podrás revertir esta acción después',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ba1a1a',
                cancelButtonColor: '#847467',
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
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteTypeCompany(id);

                    $wire.on('type-company-deleted', () => {
                        Swal.fire({
                            title: 'Eliminado',
                            text: 'El tipo de empresa fue eliminado correctamente',
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