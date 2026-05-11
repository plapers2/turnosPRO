<div>
    {{-- LOADING BAR --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- BÚSQUEDA --}}
    <div class="px-8 py-4">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-3">

                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50">search</span>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Nombre, correo o teléfono..."
                        class="w-full pl-9 pr-4 py-2 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface
                        focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" />
                </div>

                <select wire:model.live="servicio"
                    class="px-4 py-2 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface
                    focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                    <option value="">Todos los servicios</option>
                    @foreach ($servicios as $id => $nombre)
                    <option value="{{ $nombre }}">{{ $nombre }}</option>
                    @endforeach
                </select>
                <select wire:model.live="frecuente"
                    class="px-4 py-2 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface
        focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                    <option value="">Todos los clientes</option>
                    <option value="si">Frecuentes</option>
                    <option value="no">Sin frecuencia</option>
                </select>

            </div>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="px-8 pb-20">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
            shadow-[0_10px_30px_rgba(95,94,90,0.05)] overflow-hidden">

            <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide">Clientes</h3>
                <span class="text-xs text-on-surface-variant">
                    {{ $customers->total() }} {{ $customers->total() === 1 ? 'cliente' : 'clientes' }}
                </span>
            </div>

            {{-- DESKTOP --}}
            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
                wire:target="search, servicio, frecuente" class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface/50 text-on-surface-variant">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">Cliente</th>
                            <th class="px-6 py-4 text-left font-semibold">Teléfono</th>
                            <th class="px-6 py-4 text-left font-semibold">Visitas completadas</th>
                            <th class="px-6 py-4 text-left font-semibold">Servicio favorito</th>
                            <th class="px-6 py-4 text-left font-semibold">Última cita</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @forelse ($customers as $customer)
                        <tr class="hover:bg-surface/40 transition">

                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-semibold text-on-surface">{{ $customer->user->name }}</span>
                                    <span class="text-xs text-on-surface-variant">{{ $customer->user->email }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-on-surface-variant text-xs">
                                {{ $customer->user->phone }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                        {{ $customer->total_visitas > 0 ? 'bg-primary/10 text-primary' : 'bg-surface-container text-on-surface-variant' }}
                                        text-sm font-bold">
                                        {{ $customer->total_visitas }}
                                    </span>
                                    @if ($customer->total_visitas >= 5)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">
                                        <span class="material-symbols-outlined text-[11px]">star</span>
                                        Frecuente
                                    </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @if ($customer->servicio_favorito)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-primary-fixed text-on-primary-fixed text-xs font-semibold">
                                    <span class="material-symbols-outlined text-[12px]">spa</span>
                                    {{ $customer->servicio_favorito->name }}
                                </span>
                                @else
                                <span class="text-xs text-on-surface-variant italic">Sin datos</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-xs text-on-surface-variant">
                                @if ($customer->appointments->first())
                                {{ $customer->appointments->first()->start_time->format('d/m/Y H:i') }}
                                @else
                                <span class="italic">—</span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-16">
                                <div class="flex flex-col items-center gap-4">
                                    <span class="material-symbols-outlined text-4xl text-primary/30">group_off</span>
                                    <p class="text-on-surface-variant text-sm">
                                        {{ $search ? 'No se encontraron clientes con ese criterio.' : 'No hay clientes con citas registradas.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE --}}
            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
                wire:target="search" class="md:hidden space-y-4 p-4">
                @forelse ($customers as $customer)
                <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 shadow-sm">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="font-semibold text-on-surface">{{ $customer->user->name }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $customer->user->email }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $customer->user->phone }}</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-primary/10 text-primary font-bold text-sm">
                            {{ $customer->total_visitas }}
                        </span>
                    </div>
                    @if ($customer->servicio_favorito)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-primary-fixed text-on-primary-fixed text-xs font-semibold mt-1">
                        <span class="material-symbols-outlined text-[12px]">spa</span>
                        {{ $customer->servicio_favorito->name }}
                    </span>
                    @endif
                    @if ($customer->appointments->first())
                    <p class="text-xs text-on-surface-variant mt-2">
                        Última cita: {{ $customer->appointments->first()->start_time->format('d/m/Y H:i') }}
                    </p>
                    @endif
                </div>
                @empty
                <p class="text-center text-sm text-on-surface-variant py-8">No hay clientes registrados.</p>
                @endforelse
            </div>

            {{-- PAGINACIÓN --}}
            <div class="px-6 py-4 border-t border-outline-variant/20">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</div>