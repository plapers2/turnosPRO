<div>
    {{-- LOADING BAR --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- FILTROS --}}
    <div class="px-8 py-4 flex flex-col sm:flex-row gap-4">

        {{-- Estado --}}
        <select wire:model.live="status"
            class="px-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-lowest
                   text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
            <option value="">Todos los estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
        </select>

        {{-- Día --}}
        <select wire:model.live="day"
            class="px-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-lowest
                   text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
            <option value="">Todos los días</option>
            @foreach ($days as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>

    </div>

    {{-- GRID SEMANAL --}}
    <div class="px-8 pb-20">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow p-6">

            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
                wire:target="status, day" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                @foreach ($visibleDays as $key => $label)
                    <div class="bg-surface rounded-xl border border-outline-variant/20 p-5 flex flex-col">

                        {{-- HEADER DÍA --}}
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-primary">{{ $label }}</h3>
                            <span class="text-xs text-on-surface-variant">
                                {{ count($openingHours[$key] ?? []) }} horarios
                            </span>
                        </div>

                        {{-- LISTA --}}
                        <div class="space-y-3 flex-1">
                            @forelse ($openingHours[$key] ?? [] as $hour)
                                <div
                                    class="bg-surface-container-high rounded-xl px-4 py-3 flex flex-col gap-3 hover:shadow-md transition">

                                    {{-- HORARIO --}}
                                    <div class="text-sm font-semibold text-on-surface">
                                        {{ \Carbon\Carbon::parse($hour->start_time)->format('h:i A') }}
                                        <span class="text-on-surface-variant font-normal mx-1">—</span>
                                        {{ \Carbon\Carbon::parse($hour->end_time)->format('h:i A') }}
                                    </div>

                                    {{-- ESTADO --}}
                                    <div
                                        class="text-xs px-2 py-1 w-fit text-center rounded-md font-medium
                                        {{ $hour->deleted_at ? 'bg-error/20 text-on-error-container' : 'bg-indigo-400/20 text-indigo-700' }}">
                                        {{ $hour->deleted_at ? 'Inactivo' : 'Activo' }}
                                    </div>

                                    {{-- ACCIONES --}}
                                    @role('admin')
                                        <div
                                            class="flex items-center justify-end gap-4 text-xs border-t border-outline-variant/20 pt-2">
                                            @if ($hour->deleted_at)
                                                <button wire:click="restoreHour({{ $hour->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="text-green-600 font-medium hover:underline">
                                                    <span wire:loading.remove
                                                        wire:target="restoreHour({{ $hour->id }})">Restaurar</span>
                                                    <span wire:loading wire:target="restoreHour({{ $hour->id }})">
                                                        <span
                                                            class="animate-spin inline-block w-3 h-3 border-2 border-current border-t-transparent rounded-full"></span>
                                                    </span>
                                                </button>
                                            @else
                                                <a href="{{ route('opening-hours.edit', $hour->id) }}"
                                                    class="text-primary font-medium hover:underline">Editar</a>
                                                <button wire:click="confirmDelete({{ $hour->id }})"
                                                    class="text-error font-medium hover:underline">Eliminar</button>
                                            @endif
                                        </div>
                                    @endrole

                                </div>
                            @empty
                                <div class="flex-1 flex items-center justify-center py-4">
                                    <p class="text-sm text-on-surface-variant italic">Sin horarios</p>
                                </div>
                            @endforelse
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </div>

    @script
        <script>
            $wire.on('confirm-delete', ({
                id
            }) => {
                Swal.fire({
                    title: '¿Eliminar horario?',
                    text: 'Podrás restaurarlo después',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ba1a1a',
                    cancelButtonColor: '#847467',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    background: '#fcf9f3',
                    color: '#1c1c19',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.deleteHour(id);

                        $wire.on('hour-deleted', () => {
                            Swal.fire({
                                title: 'Eliminado',
                                text: 'El horario fue eliminado correctamente',
                                icon: 'success',
                                timer: 1200,
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
