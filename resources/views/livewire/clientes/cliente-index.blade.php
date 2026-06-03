<div>
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- FILTROS --}}
    <div class="px-8 py-4 flex flex-col sm:flex-row gap-4">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar cliente..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-outline-variant
                       bg-surface-container-lowest text-on-surface placeholder:text-on-surface-variant/60
                       text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition" />
        </div>
        <select wire:model.live="subscription_tier"
            class="px-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-lowest
                   text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
            <option value="">Todas las suscripciones</option>
            <option value="standard">Standard</option>
            <option value="premium">Premium</option>
        </select>
    </div>

    {{-- TABLA --}}
    <div class="px-8 pb-20">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
                    shadow-[0_10px_30px_rgba(95,94,90,0.05)] overflow-hidden">

            <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide">
                    Lista de clientes
                </h3>
            </div>

            {{-- DESKTOP --}}
            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
                wire:target="search, subscription_tier" class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface/50 text-on-surface-variant">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">Cliente</th>
                            <th class="px-6 py-4 text-left font-semibold">Teléfono</th>
                            <th class="px-6 py-4 text-left font-semibold">Empresas</th>
                            <th class="px-6 py-4 text-left font-semibold">Plan</th>
                            <th class="px-6 py-4 text-left font-semibold">Estado</th>
                            <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @forelse ($clientes as $cliente)
                        <tr class="hover:bg-surface/40 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0"
                                        style="background: #ffdcbe; color: #663a00;">
                                        {{ strtoupper(substr($cliente->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-primary">{{ $cliente->name }}</p>
                                        <p class="text-xs text-on-surface-variant">{{ $cliente->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">
                                {{ $cliente->phone ?: '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @php $empresas = $cliente->companies ?? collect(); @endphp
                                @if ($empresas->isEmpty())
                                <span class="text-xs text-on-surface-variant italic">Sin empresa</span>
                                @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($empresas->take(2) as $empresa)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-primary/10 text-primary">
                                        {{ $empresa->name }}
                                    </span>
                                    @endforeach
                                    @if ($empresas->count() > 2)
                                    <span class="text-xs text-on-surface-variant">+{{ $empresas->count() - 2 }}</span>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold
                                    {{ $cliente->subscription_tier === 'premium' ? 'bg-amber-100 text-amber-700' : 'bg-surface-variant text-on-surface-variant' }}">
                                    <span class="material-symbols-outlined text-[14px] mr-1">
                                        {{ $cliente->subscription_tier === 'premium' ? 'workspace_premium' : 'person' }}
                                    </span>
                                    {{ ucfirst($cliente->subscription_tier ?? 'standard') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold
                                    {{ $cliente->deleted_at ? 'bg-error/20 text-on-error-container' : 'bg-indigo-400/20 text-indigo-700' }}">
                                    {{ $cliente->deleted_at ? 'Inactivo' : 'Activo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="togglePlan({{ $cliente->id }})"
                                    wire:loading.attr="disabled"
                                    class="text-sm font-semibold transition
                                        {{ $cliente->subscription_tier === 'premium' ? 'text-amber-600 hover:text-amber-800' : 'text-primary hover:text-primary/70' }}">
                                    <span wire:loading.remove wire:target="togglePlan({{ $cliente->id }})">
                                        {{ $cliente->subscription_tier === 'premium' ? 'Quitar premium' : 'Dar premium' }}
                                    </span>
                                    <span wire:loading wire:target="togglePlan({{ $cliente->id }})">
                                        <span class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full"></span>
                                    </span>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-16">
                                <div class="flex flex-col items-center gap-4">
                                    <span class="material-symbols-outlined text-4xl text-primary">group</span>
                                    <p class="text-on-surface-variant">No se encontraron clientes</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE --}}
            <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search, subscription_tier"
                class="md:hidden space-y-4 p-4">
                @forelse ($clientes as $cliente)
                <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0"
                            style="background: #ffdcbe; color: #663a00;">
                            {{ strtoupper(substr($cliente->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-primary">{{ $cliente->name }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $cliente->email }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm mb-3">
                        <p class="text-on-surface-variant">
                            <span class="font-semibold">Teléfono:</span> {{ $cliente->phone ?: '—' }}
                        </p>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold
                            {{ $cliente->subscription_tier === 'premium' ? 'bg-amber-100 text-amber-700' : 'bg-surface-variant text-on-surface-variant' }}">
                            {{ ucfirst($cliente->subscription_tier ?? 'standard') }}
                        </span>
                    </div>
                    <div class="flex justify-end border-t pt-3">
                        <button wire:click="togglePlan({{ $cliente->id }})"
                            class="text-sm font-semibold
                                {{ $cliente->subscription_tier === 'premium' ? 'text-amber-600' : 'text-primary' }}">
                            {{ $cliente->subscription_tier === 'premium' ? 'Quitar premium' : 'Dar premium' }}
                        </button>
                    </div>
                </div>
                @empty
                <p class="text-center text-sm text-gray-400">No hay clientes registrados</p>
                @endforelse
            </div>

            {{-- PAGINACIÓN --}}
            <div class="px-6 py-4 border-t border-outline-variant/20">
                {{ $clientes->links() }}
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('subscription_tier-updated', () => {
            Swal.fire({
                title: 'Plan actualizado',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                background: '#fcf9f3',
                color: '#1c1c19'
            });
        });
    </script>
    @endscript
</div>