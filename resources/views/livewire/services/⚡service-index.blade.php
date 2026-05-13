<div>

    {{-- ══════════════════════════════
         LOADING BAR
    ══════════════════════════════ --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- ══════════════════════════════
         ESTADÍSTICAS
    ══════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 px-8 pt-5">

        {{-- Total --}}
        <div
            class="flex items-center gap-3 rounded-xl border border-outline-variant/50
                    bg-surface-container-lowest px-4 py-3">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center
                        rounded-[10px] bg-primary-container text-on-primary-container">
                <span class="material-symbols-outlined text-[17px]">service_toolbox</span>
            </div>
            <div class="flex flex-col gap-0.5">
                <span class="text-[17px] font-semibold leading-none text-on-surface">{{ $services->total() }}</span>
                <span class="text-[11px] font-normal text-on-surface-variant">Total</span>
            </div>
        </div>

        {{-- Activos --}}
        <div
            class="flex items-center gap-3 rounded-xl border border-outline-variant/50
                    bg-surface-container-lowest px-4 py-3">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center
                        rounded-[10px] bg-green-300 text-green-600">
                <span class="material-symbols-outlined text-[17px]">check_circle</span>
            </div>
            <div class="flex flex-col gap-0.5">
                <span class="text-[17px] font-semibold leading-none text-on-surface">{{ $activeCount }}</span>
                <span class="text-[11px] font-normal text-on-surface-variant">Activos</span>
            </div>
        </div>

        {{-- Inactivos --}}
        <div
            class="flex items-center gap-3 rounded-xl border border-outline-variant/50
                    bg-surface-container-lowest px-4 py-3">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center
                        rounded-[10px] bg-error-container/60 text-on-error-container">
                <span class="material-symbols-outlined text-[17px]">cancel</span>
            </div>
            <div class="flex flex-col gap-0.5">
                <span class="text-[17px] font-semibold leading-none text-on-surface">{{ $inactiveCount }}</span>
                <span class="text-[11px] font-normal text-on-surface-variant">Inactivos</span>
            </div>
        </div>

        {{-- Duración promedio --}}
        <div
            class="flex items-center gap-3 rounded-xl border border-outline-variant/50
                    bg-surface-container-lowest px-4 py-3">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center
                        rounded-[10px] bg-blue-200 text-blue-600">
                <span class="material-symbols-outlined text-[17px]">schedule</span>
            </div>
            <div class="flex flex-col gap-0.5">
                <span class="text-[17px] font-semibold leading-none text-on-surface">{{ $avgDuration }} min</span>
                <span class="text-[11px] font-normal text-on-surface-variant">Duración prom.</span>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════
         FILTROS
    ══════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 px-8 pt-5">

        {{-- Buscador --}}
        <div class="relative w-full sm:max-w-[300px]">
            <span
                class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2
                         -translate-y-1/2 text-[17px] text-on-surface-variant">
                search
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar servicio..."
                class="w-full rounded-xl border border-outline-variant/60
                       bg-surface-container-lowest py-2.5 pl-9 pr-4
                       text-[13px] text-on-surface placeholder:text-on-surface-variant/50
                       focus:outline-none focus:ring-2 focus:ring-primary/30 transition-shadow" />
        </div>

        {{-- Tabs de estado --}}
        <div
            class="flex gap-1 rounded-xl border border-outline-variant/60
                    bg-surface-container-lowest p-1 shrink-0">

            <button wire:click="$set('status', '')"
                class="inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5
                       text-[12.5px] font-semibold transition-all duration-150
                       {{ $status === ''
                           ? 'bg-on-primary-fixed-variant text-primary-fixed shadow-sm'
                           : 'text-on-surface-variant hover:bg-surface-container' }}">
                Todos
                <span
                    class="inline-flex h-[18px] min-w-[18px] items-center justify-center
                             rounded-full px-1 text-[10px] font-bold
                             {{ $status === '' ? 'bg-white/20 text-white' : 'bg-surface-container text-on-surface-variant' }}">
                    {{ $services->total() }}
                </span>
            </button>

            <button wire:click="$set('status', 'active')"
                class="inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5
                       text-[12.5px] font-semibold transition-all duration-150
                       {{ $status === 'active'
                           ? 'bg-on-primary-fixed-variant text-primary-fixed shadow-sm'
                           : 'text-on-surface-variant hover:bg-surface-container' }}">
                Activos
                <span
                    class="inline-flex h-[18px] min-w-[18px] items-center justify-center
                             rounded-full px-1 text-[10px] font-bold
                             {{ $status === 'active' ? 'bg-white/20 text-white' : 'bg-surface-container text-on-surface-variant' }}">
                    {{ $activeCount }}
                </span>
            </button>

            <button wire:click="$set('status', 'inactive')"
                class="inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5
                       text-[12.5px] font-semibold transition-all duration-150
                       {{ $status === 'inactive'
                           ? 'bg-on-primary-fixed-variant text-primary-fixed shadow-sm'
                           : 'text-on-surface-variant hover:bg-surface-container' }}">
                Inactivos
                <span
                    class="inline-flex h-[18px] min-w-[18px] items-center justify-center
                             rounded-full px-1 text-[10px] font-bold
                             {{ $status === 'inactive' ? 'bg-white/20 text-white' : 'bg-surface-container text-on-surface-variant' }}">
                    {{ $inactiveCount }}
                </span>
            </button>

        </div>
    </div>

    {{-- ══════════════════════════════
         GRID DE SERVICIOS
    ══════════════════════════════ --}}
    <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search, status"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 px-8 pt-5 pb-8 transition-opacity duration-200">

        @forelse ($services as $service)

            <article
                class="group flex flex-col overflow-hidden rounded-2xl border bg-surface-container-lowest
                       shadow-[0_2px_12px_rgba(0,0,0,0.04)] transition-all duration-300
                       hover:-translate-y-1 hover:shadow-[0_8px_28px_rgba(0,0,0,0.08)]
                       {{ $service->trashed()
                           ? 'border-dashed border-outline-variant/50 opacity-75'
                           : 'border-outline-variant/50 hover:border-outline-variant' }}">

                {{-- ── Imagen ── --}}
                <figure
                    class="relative m-0 h-[145px] w-full shrink-0 overflow-hidden
                               bg-surface-container">
                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}"
                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]
                               {{ $service->trashed() ? 'grayscale opacity-70' : '' }}" />

                    {{-- Badge de estado --}}
                    <span
                        class="absolute right-2.5 top-2.5 rounded-full px-2.5 py-[3px]
                                 text-[11px] font-semibold tracking-wide backdrop-blur-sm
                                 {{ $service->trashed()
                                     ? 'bg-error-container/90 text-on-error-container'
                                     : 'bg-primary-container/90 text-on-primary-container' }}">
                        {{ $service->trashed() ? 'Inactivo' : 'Activo' }}
                    </span>
                </figure>

                {{-- ── Cuerpo ── --}}
                <div class="flex flex-1 flex-col gap-3 px-[18px] py-4">

                    <div class="flex flex-col gap-1.5">
                        <h3
                            class="font-headline text-[16px] font-semibold leading-snug
                                   tracking-tight text-on-surface">
                            {{ $service->name }}
                        </h3>
                        <p class="line-clamp-2 text-[12.5px] leading-relaxed text-on-surface-variant">
                            {{ $service->description }}
                        </p>
                    </div>

                    {{-- Tags --}}
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-lg
                                     bg-primary-container/90 px-2.5 py-[5px]
                                     text-[11.5px] font-semibold text-on-primary-container">
                            <span class="material-symbols-outlined text-[13px]">schedule</span>
                            {{ $service->duration }} min
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-lg
                                     bg-secondary-container/50 px-2.5 py-[5px]
                                     text-[11.5px] font-semibold text-on-secondary-container">
                            <span class="material-symbols-outlined text-[13px]">payments</span>
                            $ {{ number_format($service->price, 0, ',', '.') }}
                        </span>
                    </div>

                </div>

                {{-- ── Footer / Acciones ── --}}
                <div
                    class="flex items-center justify-between gap-2
                            border-t border-outline-variant/40 px-[18px] py-3">

                    {{-- Indicador de estado --}}
                    @if ($service->trashed())
                        <span
                            class="inline-flex items-center gap-1.5
                                     text-[12px] font-medium text-error/80">
                            <span class="material-symbols-outlined text-[13px]">block</span>
                            Inactivo
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1.5
                                     text-[12px] font-medium text-primary/80">
                            <span class="material-symbols-outlined text-[13px]">check_circle</span>
                            Disponible
                        </span>
                    @endif

                    {{-- Botones de acción — solo admin --}}
                    @role('admin')
                        <div class="flex items-center gap-1">

                            @if ($service->trashed())
                                {{-- Restaurar --}}
                                <button wire:click="restoreService({{ $service->id }})" wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 rounded-lg border
                                           border-transparent px-3 py-1.5
                                           text-[12px] font-semibold text-green-700
                                           transition-all hover:border-green-200/70 hover:bg-green-50
                                           disabled:opacity-50">
                                    <span wire:loading.remove wire:target="restoreService({{ $service->id }})">
                                        <span class="material-symbols-outlined text-[14px] leading-none">restore</span>
                                    </span>
                                    <span wire:loading wire:target="restoreService({{ $service->id }})">
                                        <span
                                            class="block h-3.5 w-3.5 animate-spin rounded-full
                                                     border-2 border-current border-t-transparent"></span>
                                    </span>
                                    Restaurar
                                </button>
                            @else
                                {{-- Editar --}}
                                <a href="{{ route('services.edit', $service->id) }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg border
                                           border-transparent px-3 py-1.5
                                           text-[12px] font-semibold text-primary/80
                                           transition-all hover:border-primary/20 hover:bg-primary-container/40">
                                    <span class="material-symbols-outlined text-[14px] leading-none">edit</span>
                                    Editar
                                </a>

                                {{-- Eliminar --}}
                                <button wire:click="confirmDelete({{ $service->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-lg border
                                           border-transparent px-3 py-1.5
                                           text-[12px] font-semibold text-error/80
                                           transition-all hover:border-error/20 hover:bg-error-container/40">
                                    <span class="material-symbols-outlined text-[14px] leading-none">delete</span>
                                    Eliminar
                                </button>
                            @endif

                        </div>
                    @endrole

                </div>

            </article>

        @empty

            <div
                class="col-span-full flex flex-col items-center justify-center gap-4
                        rounded-2xl border border-dashed border-outline-variant/50
                        bg-surface-container-lowest px-8 py-20 text-center">
                <div
                    class="flex h-14 w-14 items-center justify-center
                            rounded-full bg-surface-container text-on-surface-variant">
                    <span class="material-symbols-outlined text-[26px]">service_toolbox</span>
                </div>
                <div class="flex flex-col gap-1">
                    <h3 class="font-headline text-[17px] font-semibold text-on-surface">
                        Sin resultados
                    </h3>
                    <p class="max-w-[260px] text-[13px] leading-relaxed text-on-surface-variant">
                        No se encontraron servicios. Intenta con otro término o cambia el filtro.
                    </p>
                </div>
            </div>

        @endforelse

    </div>

    {{-- ══════════════════════════════
         PAGINACIÓN
    ══════════════════════════════ --}}
    @if ($services->hasPages())
        <div class="border-t border-outline-variant/40 px-8 py-5">
            {{ $services->links() }}
        </div>
    @endif

    {{-- ══════════════════════════════
         SWEETALERT
    ══════════════════════════════ --}}
    @script
        <script>
            $wire.on('confirm-delete', ({
                id
            }) => {
                Swal.fire({
                    title: '¿Eliminar servicio?',
                    text: 'Podrás restaurarlo después si lo necesitas.',
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
                        cancelButton: 'px-4 py-2 rounded-lg',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.deleteService(id);

                        $wire.on('service-deleted', () => {
                            Swal.fire({
                                title: 'Eliminado',
                                text: 'El servicio fue eliminado correctamente.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                background: '#fcf9f3',
                                color: '#1c1c19',
                            });
                        });

                        $wire.on('delete-error', ({
                            message
                        }) => {
                            Swal.fire({
                                title: 'No se puede eliminar',
                                text: message,
                                icon: 'error',
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#ba1a1a',
                                background: '#fcf9f3',
                                color: '#1c1c19',
                            });
                        });
                    }
                });
            });
        </script>
    @endscript

</div>
