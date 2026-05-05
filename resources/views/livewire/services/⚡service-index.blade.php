<div>
    <div>
        {{-- LOADING BAR --}}
        <div wire:loading.delay class="loading-bar">
            <div class="loading-bar__progress"></div>
        </div>
        {{-- FILTROS --}}
        <div class="px-8 pt-6 pb-2 flex flex-col sm:flex-row gap-4">

            <div class="relative flex-1">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">
                    search
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar servicio..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-outline-variant
                       bg-surface-container-lowest text-on-surface placeholder:text-on-surface-variant/60
                       text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition" />
            </div>

            <select wire:model.live="status"
                class="px-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-lowest
                   text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
                <option value="">Todos</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
        </div>
    </div>


    {{-- GRID --}}
    <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
        wire:target="search, status" class="p-8 pb-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            @forelse ($services as $service)
                <article
                    class="bg-surface-container-lowest rounded-xl flex flex-col shadow-[0_10px_30px_rgba(95,94,90,0.06)] transition-all hover:-translate-y-1 duration-300">
                    <figure class="w-full h-48 overflow-hidden rounded-t-xl">
                        <img alt="{{ $service->name }}" class="w-full h-full object-cover"
                            src="{{ asset('storage/' . $service->image) }}" />
                    </figure>

                    <div class="p-6 flex flex-col gap-4 flex-1">
                        <div>
                            <h3 class="text-xl font-bold text-primary mb-2 font-headline tracking-tight">
                                {{ $service->name }}
                            </h3>
                            <p class="text-on-surface-variant text-sm leading-relaxed line-clamp-2">
                                {{ $service->description }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3 mt-auto">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-primary-fixed text-on-primary-fixed text-xs font-semibold font-label">
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                {{ $service->duration }} Min
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-secondary-fixed text-on-secondary-fixed text-xs font-semibold font-label">
                                <span class="material-symbols-outlined text-[16px]">payments</span>
                                {{ $service->price }}
                            </span>
                        </div>

                        <div>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold
                                {{ $service->deleted_at ? 'bg-error/20 text-on-error-container' : 'bg-indigo-400/20 text-indigo-700' }}">
                                {{ $service->deleted_at ? 'Inactivo' : 'Activo' }}
                            </span>
                        </div>

                        @role('admin')
                            <div class="flex justify-end gap-4 pt-4 mt-2">
                                @if ($service->trashed())
                                    <button wire:click="restoreService({{ $service->id }})" wire:loading.attr="disabled"
                                        class="text-sm font-semibold text-green-600 hover:text-green-800 transition">
                                        <span wire:loading.remove
                                            wire:target="restoreService({{ $service->id }})">Restaurar</span>
                                        <span wire:loading wire:target="restoreService({{ $service->id }})">
                                            <span
                                                class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full"></span>
                                        </span>
                                    </button>
                                @else
                                    <a href="{{ route('services.edit', $service->id) }}"
                                        class="text-sm font-semibold text-primary hover:text-primary-container transition-colors px-2 py-1 rounded">
                                        Editar
                                    </a>
                                    <button wire:click="confirmDelete({{ $service->id }})"
                                        class="text-sm font-semibold text-error hover:text-on-error-container transition-colors px-2 py-1 rounded">
                                        Eliminar
                                    </button>
                                @endif
                            </div>
                        @endrole
                    </div>
                </article>

            @empty
                <div
                    class="col-span-full flex flex-col items-center justify-center text-center py-20 px-6
                    bg-surface-container-lowest rounded-xl border border-outline-variant/20">
                    <div
                        class="w-16 h-16 flex items-center justify-center rounded-full bg-primary/10 text-primary mb-6">
                        <span class="material-symbols-outlined">service_toolbox</span>
                    </div>
                    <h3 class="text-xl font-semibold text-primary mb-2">No se encontraron servicios</h3>
                    <p class="text-sm text-on-surface-variant max-w-md mb-6 leading-relaxed">
                        Intenta con otro término o cambia el filtro de estado.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-10">{{ $services->links() }}</div>
    </div>
</div>


@script
    <script>
        $wire.on('confirm-delete', ({
            id
        }) => {
            Swal.fire({
                title: '¿Eliminar servicio?',
                text: 'Esta acción no se puede deshacer',
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
                    $wire.deleteService(id);

                    // Swal de éxito lo disparamos desde el evento de Livewire
                    $wire.on('service-deleted', () => {
                        Swal.fire({
                            title: 'Eliminado',
                            text: 'El servicio fue eliminado correctamente',
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
