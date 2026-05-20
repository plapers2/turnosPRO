<div class="w-full bg-white border-b border-gray-200 px-4 py-3">
    <div class="flex flex-wrap items-center gap-3">

        {{-- Toggle semana / mes --}}
        <div class="flex rounded-lg border border-gray-300 overflow-hidden text-sm font-medium">
            <button
                wire:click="$set('availabilityView', 'week')"
                @class([
                    'px-4 py-2 transition-colors',
                    'bg-indigo-600 text-white'   => $availabilityView === 'week',
                    'bg-white text-gray-700 hover:bg-gray-50' => $availabilityView !== 'week',
                ])>
                Semana
            </button>
            <button
                wire:click="$set('availabilityView', 'month')"
                @class([
                    'px-4 py-2 transition-colors border-l border-gray-300',
                    'bg-indigo-600 text-white'   => $availabilityView === 'month',
                    'bg-white text-gray-700 hover:bg-gray-50' => $availabilityView !== 'month',
                ])>
                Mes
            </button>
        </div>

        {{-- Navegación de período --}}
        <div class="flex items-center gap-2">
            <button
                wire:click="availabilityPrev"
                class="p-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 transition-colors"
                title="Período anterior">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button
                wire:click="availabilityToday"
                class="px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium transition-colors">
                Hoy
            </button>
            <button
                wire:click="availabilityNext"
                class="p-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 transition-colors"
                title="Período siguiente">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Etiqueta del período actual --}}
        <span class="text-sm font-semibold text-gray-800">
            {{ $availabilitySummary['period_label'] }}
        </span>

        {{-- Filtro por profesional (solo admin) --}}
        @if ($isAdmin)
            <div class="flex-1 min-w-[200px] max-w-xs">
                <select
                    wire:model.live="filterProfessional"
                    class="w-full text-sm rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— Todos los profesionales —</option>
                    @foreach ($professionals as $pro)
                        <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- Duración del slot --}}
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600 whitespace-nowrap">Duración slot:</label>
            <select
                wire:model.live="slotMinutes"
                class="text-sm rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="15">15 min</option>
                <option value="30">30 min</option>
                <option value="45">45 min</option>
                <option value="60">60 min</option>
            </select>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════
     TARJETAS DE RESUMEN (KPIs del período)
═══════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 px-4 py-3">

    <div class="bg-white rounded-xl border border-gray-200 p-3 text-center shadow-sm">
        <p class="text-2xl font-bold text-gray-900">{{ $availabilitySummary['total_slots'] }}</p>
        <p class="text-xs text-gray-500 mt-0.5">Slots totales</p>
    </div>

    <div class="bg-emerald-50 rounded-xl border border-emerald-200 p-3 text-center shadow-sm">
        <p class="text-2xl font-bold text-emerald-700">{{ $availabilitySummary['free_slots'] }}</p>
        <p class="text-xs text-emerald-600 mt-0.5">Slots libres</p>
    </div>

    <div class="bg-rose-50 rounded-xl border border-rose-200 p-3 text-center shadow-sm">
        <p class="text-2xl font-bold text-rose-700">{{ $availabilitySummary['busy_slots'] }}</p>
        <p class="text-xs text-rose-600 mt-0.5">Slots ocupados</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-3 text-center shadow-sm">
        <p class="text-2xl font-bold text-gray-900">{{ $availabilitySummary['occupancy_pct'] }}%</p>
        <p class="text-xs text-gray-500 mt-0.5">Ocupación</p>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     LEYENDA
═══════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-center gap-4 px-4 pb-2 text-xs text-gray-500">
    <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span> Disponible (&lt; 60% ocupado)
    </span>
    <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> Parcial (≥ 60% ocupado)
    </span>
    <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-rose-400 inline-block"></span> Lleno
    </span>
    <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-gray-300 inline-block"></span> No laboral
    </span>
</div>

{{-- ═══════════════════════════════════════════════
     GRID DE DÍAS
═══════════════════════════════════════════════ --}}
<div @class([
    'grid gap-3 px-4 pb-6',
    'grid-cols-7'                     => $availabilityView === 'week',
    'grid-cols-7 sm:grid-cols-7'      => $availabilityView === 'month',
])>
    @foreach ($availabilityDays as $day)
        @php
            $statusClasses = match ($day['status']) {
                'available' => 'bg-emerald-50  border-emerald-300  ring-emerald-200',
                'partial'   => 'bg-amber-50    border-amber-300    ring-amber-200',
                'full'      => 'bg-rose-50     border-rose-300     ring-rose-200',
                default     => 'bg-gray-50     border-gray-200     ring-gray-100 opacity-60',
            };
            $badgeClasses = match ($day['status']) {
                'available' => 'bg-emerald-100 text-emerald-700',
                'partial'   => 'bg-amber-100   text-amber-700',
                'full'      => 'bg-rose-100    text-rose-700',
                default     => 'bg-gray-100    text-gray-400',
            };
            $isToday = $day['date'] === now()->toDateString();
        @endphp

        <div
            x-data="{ open: false }"
            class="relative rounded-xl border {{ $statusClasses }} ring-1 cursor-pointer
                   transition-shadow hover:shadow-md select-none"
            @click="open = !open">

            {{-- Cabecera del día --}}
            <div class="px-3 pt-3 pb-2">
                <div class="flex items-center justify-between">
                    <span @class([
                        'text-xs font-semibold',
                        'text-indigo-700' => $isToday,
                        'text-gray-700'   => !$isToday,
                    ])>
                        {{ $day['label'] }}
                        @if ($isToday)
                            <span class="ml-1 px-1 py-0.5 rounded bg-indigo-600 text-white text-[10px]">HOY</span>
                        @endif
                    </span>
                    @if ($day['is_working_day'])
                        <span class="text-[11px] font-medium px-1.5 py-0.5 rounded-full {{ $badgeClasses }}">
                            {{ $day['free_slots'] }} libres
                        </span>
                    @endif
                </div>

                @if ($day['is_working_day'])
                    {{-- Barra de progreso de ocupación --}}
                    <div class="mt-2 h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-300
                                {{ $day['status'] === 'full' ? 'bg-rose-500' : ($day['status'] === 'partial' ? 'bg-amber-500' : 'bg-emerald-500') }}"
                            style="width: {{ $day['occupancy_pct'] }}%">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">{{ $day['occupancy_pct'] }}% ocupado</p>
                @else
                    <p class="text-[11px] text-gray-400 mt-1">No laboral</p>
                @endif
            </div>

            {{-- Detalle de slots (expandible al hacer clic) --}}
            @if ($day['is_working_day'] && count($day['slots']) > 0)
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-cloak
                    class="border-t border-gray-200 px-3 py-2 max-h-48 overflow-y-auto">

                    <div class="grid grid-cols-2 gap-1">
                        @foreach ($day['slots'] as $slot)
                            <div @class([
                                'flex items-center gap-1 px-2 py-1 rounded text-[11px] font-medium',
                                'bg-emerald-100 text-emerald-700' => $slot['free'],
                                'bg-rose-100    text-rose-600    line-through opacity-70' => !$slot['free'],
                            ])>
                                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0
                                    {{ $slot['free'] ? 'bg-emerald-500' : 'bg-rose-400' }}">
                                </span>
                                {{ $slot['time'] }}
                            </div>
                        @endforeach
                    </div>

                </div>
            @endif

        </div>
    @endforeach
</div>
