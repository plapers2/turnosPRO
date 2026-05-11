<div>
    {{-- LOADING BAR --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- FILTROS --}}
    <div class="px-8 pt-4 pb-2 flex flex-col sm:flex-row gap-3">

        {{-- Estado --}}
        <div class="relative">
            <select wire:model.live="status"
                class="appearance-none pl-4 pr-9 py-2.5 rounded-xl border-[1.5px] border-outline-variant
                       bg-surface-container-lowest text-on-surface text-[13px] font-medium
                       focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10
                       transition-all cursor-pointer min-w-[160px]">
                <option value="">Todos los estados</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
            <span
                class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant">
                expand_more
            </span>
        </div>

        {{-- Día --}}
        <div class="relative">
            <select wire:model.live="day"
                class="appearance-none pl-4 pr-9 py-2.5 rounded-xl border-[1.5px] border-outline-variant
                       bg-surface-container-lowest text-on-surface text-[13px] font-medium
                       focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10
                       transition-all cursor-pointer min-w-[160px]">
                <option value="">Todos los días</option>
                @foreach ($days as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <span
                class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant">
                expand_more
            </span>
        </div>

    </div>

    {{-- GRID SEMANAL --}}
    <div class="px-8 pb-20">
        <div
            class="bg-surface-container-lowest rounded-2xl border border-outline-variant/20
                    shadow-[0_2px_16px_rgba(95,94,90,0.05)] p-6">

            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
                wire:target="status, day" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

                @foreach ($visibleDays as $key => $label)
                    <div class="bg-surface rounded-2xl border border-outline-variant/25 p-4 flex flex-col gap-3">

                        {{-- HEADER DÍA --}}
                        <div class="flex items-center justify-between">
                            <h3 class="text-[13.5px] font-semibold text-primary flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[15px] opacity-70">calendar_today</span>
                                {{ $label }}
                            </h3>
                            <span
                                class="text-[11px] font-semibold px-2.5 py-1 rounded-full
                                         bg-primary-fixed text-primary">
                                {{ count($openingHours[$key] ?? []) }} horarios
                            </span>
                        </div>

                        <div class="h-px bg-outline-variant/30"></div>

                        {{-- LISTA --}}
                        <div class="space-y-2.5">
                            @forelse ($openingHours[$key] ?? [] as $hour)
                                <div
                                    class="bg-surface-container-lowest rounded-xl border border-outline-variant/25
                                            px-3.5 py-3 flex flex-col gap-2.5
                                            hover:shadow-[0_4px_16px_rgba(95,94,90,0.08)]
                                            hover:-translate-y-px transition-all duration-200">

                                    {{-- TIEMPO + DURACIÓN --}}
                                    <div class="flex items-center gap-1.5">
                                        <span
                                            class="material-symbols-outlined text-[14px] text-on-surface-variant">schedule</span>
                                        <span class="text-[13px] font-semibold text-on-surface">
                                            {{ \Carbon\Carbon::parse($hour->start_time)->format('h:i A') }}
                                        </span>
                                        <span class="text-on-surface-variant text-[12px]">—</span>
                                        <span class="text-[13px] font-semibold text-on-surface">
                                            {{ \Carbon\Carbon::parse($hour->end_time)->format('h:i A') }}
                                        </span>
                                        <span
                                            class="ml-auto text-[11px] font-medium text-on-surface-variant
                                                     bg-surface-container px-2 py-0.5 rounded-md">
                                            {{ \Carbon\Carbon::parse($hour->start_time)->diffInMinutes(\Carbon\Carbon::parse($hour->end_time)) }}
                                            min
                                        </span>
                                    </div>

                                    {{-- ESTADO + ACCIONES --}}
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="text-[11px] font-semibold px-2.5 py-1 rounded-[7px]
                                            {{ $hour->deleted_at ? 'bg-error-container text-on-error-container' : 'bg-indigo-50 text-indigo-700' }}">
                                            {{ $hour->deleted_at ? 'Inactivo' : 'Activo' }}
                                        </span>

                                        @role('admin')
                                            <div class="flex items-center gap-1.5">
                                                @if ($hour->deleted_at)
                                                    <button wire:click="restoreHour({{ $hour->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="inline-flex items-center gap-1 text-[12px] font-semibold
                                                               px-2.5 py-1.5 rounded-lg
                                                               bg-green-50 text-green-700
                                                               hover:bg-green-100 transition-colors
                                                               disabled:opacity-50">
                                                        <span wire:loading.remove
                                                            wire:target="restoreHour({{ $hour->id }})">
                                                            <span
                                                                class="material-symbols-outlined text-[13px] leading-none">restore</span>
                                                        </span>
                                                        <span wire:loading wire:target="restoreHour({{ $hour->id }})">
                                                            <span
                                                                class="animate-spin inline-block w-3 h-3 border-2 border-current border-t-transparent rounded-full"></span>
                                                        </span>
                                                        Restaurar
                                                    </button>
                                                @else
                                                    <a href="{{ route('opening-hours.edit', $hour->id) }}"
                                                        class="inline-flex items-center gap-1 text-[12px] font-semibold
                                                               px-2.5 py-1.5 rounded-lg
                                                               bg-primary/8 text-primary
                                                               hover:bg-primary/14 transition-colors">
                                                        <span
                                                            class="material-symbols-outlined text-[13px] leading-none">edit</span>
                                                        Editar
                                                    </a>
                                                    <button wire:click="confirmDelete({{ $hour->id }})"
                                                        class="inline-flex items-center gap-1 text-[12px] font-semibold
                                                               px-2.5 py-1.5 rounded-lg
                                                               bg-error/7 text-error
                                                               hover:bg-error/13 transition-colors">
                                                        <span
                                                            class="material-symbols-outlined text-[13px] leading-none">delete</span>
                                                        Eliminar
                                                    </button>
                                                @endif
                                            </div>
                                        @endrole
                                    </div>

                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center py-6 gap-2 text-center">
                                    <span
                                        class="material-symbols-outlined text-[24px] text-on-surface-variant/40">schedule_off</span>
                                    <p class="text-[12px] text-on-surface-variant italic">Sin horarios registrados</p>
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
                    }
                });
            });

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

            $wire.on('delete-error', ({
                message
            }) => {
                Swal.fire({
                    title: 'No se puede eliminar',
                    text: message,
                    confirmButtonText: "Entendido",
                    icon: 'error',
                    confirmButtonColor: '#ba1a1a',
                    background: '#fcf9f3',
                    color: '#1c1c19'
                });
            });
        </script>
    @endscript
</div>
