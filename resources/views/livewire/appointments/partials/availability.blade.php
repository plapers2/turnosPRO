{{-- ═══════════════════════════════════════════════
     TOOLBAR DE DISPONIBILIDAD
═══════════════════════════════════════════════ --}}
<div class="w-full bg-white border-b border-gray-200 px-4 py-3">
    <div class="flex flex-wrap items-center gap-2 sm:gap-3">

        {{-- Toggle semana / mes --}}
        <div class="flex rounded-lg border border-gray-300 overflow-hidden text-sm font-medium">
            <button wire:click="$set('availabilityView', 'week')" @class([
                'px-3 sm:px-4 py-2 transition-colors',
                'bg-amber-800 text-white' => $availabilityView === 'week',
                'bg-white text-gray-700 hover:bg-gray-50' => $availabilityView !== 'week',
            ])>
                Semana
            </button>
            <button wire:click="$set('availabilityView', 'month')" @class([
                'px-3 sm:px-4 py-2 transition-colors border-l border-gray-300',
                'bg-amber-800 text-white' => $availabilityView === 'month',
                'bg-white text-gray-700 hover:bg-gray-50' => $availabilityView !== 'month',
            ])>
                Mes
            </button>
        </div>

        {{-- Navegación de período --}}
        <div class="flex items-center gap-1.5 sm:gap-2">
            <button wire:click="availabilityPrev"
                class="p-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 transition-colors"
                title="Período anterior">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button wire:click="availabilityToday"
                class="px-2.5 sm:px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium transition-colors">
                Hoy
            </button>
            <button wire:click="availabilityNext"
                class="p-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 transition-colors"
                title="Período siguiente">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        {{-- Etiqueta del período actual --}}
        <span class="text-sm font-semibold text-gray-800 w-full sm:w-auto order-first sm:order-none">
            {{ $availabilitySummary['period_label'] }}
        </span>

        {{-- Filtro por profesional (solo admin) --}}
        @if ($isAdmin)
            <div class="w-full sm:w-auto sm:flex-1 sm:min-w-[180px] sm:max-w-xs">
                <select wire:model.live="filterProfessional"
                    class="w-full text-sm rounded-lg border-gray-300 focus:ring-amber-700 focus:border-amber-700">
                    <option value="">— Todos los profesionales —</option>
                    @foreach ($professionals as $pro)
                        <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- Selector de servicio --}}
        <div class="w-full sm:w-auto flex items-center gap-2">
            <label class="text-sm text-gray-600 whitespace-nowrap">Servicio:</label>
            <select wire:model.live="filterService"
                class="flex-1 sm:flex-none text-sm rounded-lg border-gray-300 focus:ring-amber-700 focus:border-amber-700">
                <option value="">— Todos —</option>
                @foreach ($availableServices as $service)
                    <option value="{{ $service->id }}">
                        {{ $service->name }} ({{ $service->duration }} min)
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Indicador / selector de duración de slot --}}
        @if ($filterService)
            <div
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ $slotMinutes }} min / slot</span>
            </div>
        @else
            <div class="w-full sm:w-auto flex items-center gap-2">
                <label class="text-sm text-gray-600 whitespace-nowrap">Duración slot:</label>
                <select wire:model.live="slotMinutes"
                    class="flex-1 sm:flex-none text-sm rounded-lg border-gray-300 focus:ring-amber-700 focus:border-amber-700">
                    <option value="15">15 min</option>
                    <option value="30">30 min</option>
                    <option value="45">45 min</option>
                    <option value="60">60 min</option>
                </select>
            </div>
        @endif

    </div>
</div>

{{-- ═══════════════════════════════════════════════
     CHIP DE SERVICIO ACTIVO
═══════════════════════════════════════════════ --}}
@if ($availabilitySummary['service_name'])
    <div class="flex flex-wrap items-center gap-2 px-4 pt-4">
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-medium">
            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Servicio: {{ $availabilitySummary['service_name'] }}
            &nbsp;·&nbsp; slots de {{ $availabilitySummary['slot_minutes'] }} min
        </span>
        <button wire:click="$set('filterService', '')"
            class="text-xs text-gray-400 hover:text-gray-600 underline transition-colors">
            Quitar filtro
        </button>
    </div>
@endif

{{-- ═══════════════════════════════════════════════
     TARJETAS KPI
═══════════════════════════════════════════════ --}}
@php
    $availability_cards = [
        [
            'label' => 'Slots totales',
            'value' => $availabilitySummary['total_slots'],
            'icon' =>
                '<rect x="1" y="3" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M5 3V2M11 3V2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M1 7h14" stroke="currentColor" stroke-width="1.5"/>',
            'bg' => 'bg-primary-container',
            'text' => 'text-on-primary-container',
            'num' => 'text-primary',
        ],
        [
            'label' => 'Slots libres',
            'value' => $availabilitySummary['free_slots'],
            'icon' =>
                '<path d="M2.5 8.5l3.5 3.5 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
            'bg' => 'bg-[#E1F5EE]',
            'text' => 'text-[#0F6E56]',
            'num' => 'text-[#0F6E56]',
        ],
        [
            'label' => 'Slots ocupados',
            'value' => $availabilitySummary['busy_slots'],
            'icon' =>
                '<path d="M3 3l10 10M13 3L3 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
            'bg' => 'bg-[#FCEBEB]',
            'text' => 'text-[#A32D2D]',
            'num' => 'text-[#A32D2D]',
        ],
        [
            'label' => 'Ocupación',
            'value' => $availabilitySummary['occupancy_pct'] . '%',
            'icon' =>
                '<circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.3" stroke-dasharray="3 2"/><path d="M4.5 8.2l2.8 2.8 4.2-5.2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
            'bg' => 'bg-[#E6F1FB]',
            'text' => 'text-[#185FA5]',
            'num' => 'text-[#185FA5]',
        ],
    ];
@endphp

<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-5 my-5">
    @foreach ($availability_cards as $card)
        <div
            class="flex items-center gap-3 rounded-xl border border-outline-variant/40
                    bg-surface-container-lowest px-3 sm:px-4 py-3
                    shadow-[0_1px_4px_rgba(95,94,90,0.05)]">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] {{ $card['bg'] }} {{ $card['text'] }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    {!! $card['icon'] !!}
                </svg>
            </div>
            <div class="flex flex-col gap-0.5 min-w-0">
                <span class="text-[15px] sm:text-[17px] font-semibold leading-none {{ $card['num'] }}">
                    {{ $card['value'] }}
                </span>
                <span class="text-[10px] sm:text-[11px] font-normal text-on-surface-variant truncate">
                    {{ $card['label'] }}
                </span>
            </div>
        </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════
     LEYENDA
═══════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-center gap-x-4 gap-y-2 px-4 pb-2 text-xs text-gray-500">
    <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block shrink-0"></span> Disponible (&lt; 60% ocupado)
    </span>
    <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-amber-400 inline-block shrink-0"></span> Parcial (≥ 60% ocupado)
    </span>
    <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-rose-400 inline-block shrink-0"></span> Lleno
    </span>
    <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-gray-300 inline-block shrink-0"></span> No laboral
    </span>
</div>

{{-- ═══════════════════════════════════════════════
     GRID DE DÍAS
═══════════════════════════════════════════════ --}}
<div @class([
    'grid gap-2 sm:gap-3 px-4 pb-6',
    'grid-cols-3 xs:grid-cols-4 sm:grid-cols-7' => $availabilityView === 'week',
    'grid-cols-3 xs:grid-cols-4 sm:grid-cols-5 md:grid-cols-7' =>
        $availabilityView === 'month',
])>
    @foreach ($availabilityDays as $day)
        @php
            $statusClasses = match ($day['status']) {
                'available' => 'bg-emerald-50  border-emerald-300  ring-emerald-200',
                'partial' => 'bg-amber-50    border-amber-300    ring-amber-200',
                'full' => 'bg-rose-50     border-rose-300     ring-rose-200',
                default => 'bg-gray-50     border-gray-200     ring-gray-100 opacity-60',
            };
            $badgeClasses = match ($day['status']) {
                'available' => 'bg-emerald-100 text-emerald-700',
                'partial' => 'bg-amber-100   text-amber-700',
                'full' => 'bg-rose-100    text-rose-700',
                default => 'bg-gray-100    text-gray-400',
            };
            $isToday = $day['date'] === now()->toDateString();
        @endphp

        <div x-data="{ open: false }"
            class="relative rounded-xl border {{ $statusClasses }} ring-1 cursor-pointer
                   transition-shadow hover:shadow-md select-none"
            @click="open = !open">

            {{-- Cabecera del día --}}
            <div class="px-2 sm:px-3 pt-2.5 sm:pt-3 pb-2">
                <div class="flex items-start justify-between gap-1">
                    <span @class([
                        'text-[11px] sm:text-xs font-semibold leading-tight',
                        'text-amber-800' => $isToday,
                        'text-gray-700' => !$isToday,
                    ])>
                        {{ $day['label'] }}
                        @if ($isToday)
                            <span
                                class="block sm:inline mt-0.5 sm:mt-0 sm:ml-1 px-1 py-0.5 rounded bg-amber-800 text-white text-[9px] sm:text-[10px]">HOY</span>
                        @endif
                    </span>
                    @if ($day['is_working_day'])
                        <span
                            class="text-[10px] sm:text-[11px] font-medium px-1 sm:px-1.5 py-0.5 rounded-full shrink-0 {{ $badgeClasses }}">
                            {{ $day['free_slots'] }}
                        </span>
                    @endif
                </div>

                @if ($day['is_working_day'])
                    <div class="mt-2 h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300
                                {{ $day['status'] === 'full' ? 'bg-rose-500' : ($day['status'] === 'partial' ? 'bg-amber-500' : 'bg-emerald-500') }}"
                            style="width: {{ $day['occupancy_pct'] }}%">
                        </div>
                    </div>
                    <p class="text-[9px] sm:text-[10px] text-gray-400 mt-1">{{ $day['occupancy_pct'] }}% ocup.</p>
                @else
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-1">No laboral</p>
                @endif
            </div>

            {{-- Detalle de slots (expandible) --}}
            @if ($day['is_working_day'] && count($day['slots']) > 0)
                <div x-show="open" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0" x-cloak
                    class="border-t border-gray-200 px-2 sm:px-3 py-2 max-h-48 overflow-y-auto">

                    <p class="text-[10px] text-gray-400 mb-1.5 font-medium">
                        Slots de {{ $slotMinutes }} min
                        @if ($availabilitySummary['service_name'])
                            · {{ $availabilitySummary['service_name'] }}
                        @endif
                    </p>

                    <div @class([
                        'grid gap-1',
                        'grid-cols-1' => $availabilityView === 'week',
                        'grid-cols-2' => $availabilityView === 'month',
                    ])>
                        @foreach ($day['slots'] as $slot)
                            <div @class([
                                'flex flex-col px-1.5 sm:px-2 py-1 rounded text-[10px] font-medium leading-tight',
                                'bg-emerald-100 text-emerald-700' => $slot['free'],
                                'bg-rose-100    text-rose-600 line-through opacity-70' => !$slot['free'],
                            ])>
                                <span class="flex items-center gap-1">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full flex-shrink-0
                                        {{ $slot['free'] ? 'bg-emerald-500' : 'bg-rose-400' }}">
                                    </span>
                                    {{ $slot['time'] }}
                                </span>
                                @if (isset($slot['time_end']))
                                    <span class="opacity-60 pl-2.5">– {{ $slot['time_end'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
            @endif

        </div>
    @endforeach
</div>
