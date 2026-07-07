<div>
    {{-- LOADING BAR --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- SUCCESS TOAST --}}
    @if ($successMsg)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3
       bg-[#ECFDF5] text-[#065F46] border border-[#6EE7B7]
       text-sm font-medium px-4 py-3 rounded-xl shadow-md max-w-xs w-full mx-4">
            <span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle; margin-right:6px;">
                check_circle
            </span>
            <span class="flex-1">{{ $successMsg }}</span>
        </div>
    @endif

    {{-- ── ERROR TOAST ─────────────────────────────────────────────────── --}}
    @if ($errorMsg)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3
       bg-[#FEF2F2] text-[#991B1B] border border-[#FCA5A5]
       text-sm font-medium px-4 py-3 rounded-xl shadow-md max-w-xs w-full mx-4">
            <svg class="w-4 h-4 flex-shrink-0 text-[#DC2626]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
            <span class="flex-1">{{ $errorMsg }}</span>
        </div>
    @endif

    <div class="p-6 pb-24 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ══════════════════════════════════════════
                 COLUMNA PRINCIPAL
            ══════════════════════════════════════════ --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- ── EMPRESA (solo lectura) ───────────────────────────── --}}
                @if ($companyData)
                    <div
                        class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm px-5 py-3.5 flex items-center gap-4">
                        <div
                            class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-primary/10 flex items-center justify-center border border-outline-variant/20">
                            @if ($companyData['logo'])
                                <img src="{{ $companyData['logo'] }}" alt="{{ $companyData['name'] }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span
                                    class="text-xs font-bold text-primary/50">{{ strtoupper(substr($companyData['name'], 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-on-surface">{{ $companyData['name'] }}</p>
                            @if ($companyData['address'])
                                <p class="text-xs text-on-surface-variant">{{ $companyData['address'] }}</p>
                            @endif
                        </div>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-primary/10 text-primary text-[11px] font-semibold">
                            <span class="material-symbols-outlined text-[12px]">business</span>
                            Empresa activa
                        </span>
                    </div>
                @endif

                {{-- ── PASO 1: CLIENTE ──────────────────────────────────── --}}
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm">
                    <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-on-surface font-headline">
                            <span
                                class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[10px] font-bold mr-2">1</span>
                            Cliente
                        </h3>
                        @if ($clienteData)
                            <button wire:click="limpiarCliente"
                                class="text-xs text-on-surface-variant hover:text-primary transition flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">edit</span>
                                Cambiar
                            </button>
                        @endif
                    </div>

                    <div class="p-5">
                        @if ($clienteData)
                            {{-- Cliente seleccionado --}}
                            <div
                                class="flex items-center gap-3 p-3 rounded-xl bg-surface-container border border-outline-variant/20">
                                <div
                                    class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span
                                        class="text-sm font-bold text-primary/60">{{ strtoupper(substr($clienteData['name'], 0, 1)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-on-surface">{{ $clienteData['name'] }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ $clienteData['email'] }}
                                        @if ($clienteData['phone'])
                                            · {{ $clienteData['phone'] }}
                                        @endif
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-green-100 text-green-700 text-[11px] font-semibold">
                                    <span class="material-symbols-outlined text-[12px]">check_circle</span>
                                    Seleccionado
                                </span>
                            </div>
                        @else
                            {{-- Buscador --}}
                            <div class="relative" x-data="{ open: false }"
                                x-on:mousedown.outside="open = false; $wire.call('cerrarBuscador')">
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant/50 text-[18px]">search</span>
                                    <input wire:model.live.debounce.300ms="clienteSearch"
                                        wire:focus="cargarTodosClientes" x-on:focus="open = true" type="text"
                                        placeholder="Buscar por nombre, email o teléfono..."
                                        class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-surface-container border border-outline-variant/30
                                           text-sm text-on-surface placeholder:text-on-surface-variant/50
                                           focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                                    <div wire:loading wire:target="updatedClienteSearch"
                                        class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <span
                                            class="animate-spin inline-block w-4 h-4 border-2 border-primary border-t-transparent rounded-full"></span>
                                    </div>
                                </div>

                                @if (count($clienteResultados) > 0)
                                    <div
                                        class="absolute z-20 w-full mt-1 bg-surface-container-lowest border border-outline-variant/20 rounded-xl shadow-lg overflow-hidden max-h-72 overflow-y-auto">
                                        @foreach ($clienteResultados as $resultado)
                                            <button type="button" data-user-id="{{ $resultado['user_id'] }}"
                                                onmousedown="Livewire.dispatch('seleccionarClienteEvt', { userId: +this.dataset.userId })"
                                                class="w-full text-left flex items-center gap-3 px-4 py-3 hover:bg-surface-container transition border-b border-outline-variant/10 last:border-0">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                    <span
                                                        class="text-xs font-bold text-primary/60">{{ strtoupper(substr($resultado['name'], 0, 1)) }}</span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-on-surface">
                                                        {{ $resultado['name'] }}</p>
                                                    <p class="text-xs text-on-surface-variant truncate">
                                                        {{ $resultado['email'] }}
                                                        @if ($resultado['phone'])
                                                            · {{ $resultado['phone'] }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif(strlen(trim($clienteSearch)) >= 2)
                                    <div
                                        class="absolute z-20 w-full mt-1 bg-surface-container-lowest border border-outline-variant/20 rounded-xl shadow-lg px-4 py-3">
                                        <p class="text-xs text-on-surface-variant text-center">Sin resultados para
                                            "{{ $clienteSearch }}"</p>
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs text-on-surface-variant mt-2">Solo aparecen clientes registrados en esta
                                empresa.</p>
                        @endif
                    </div>
                </div>

                {{-- ── PASO 2: SERVICIOS ────────────────────────────────── --}}
                <div
                    class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-on-surface font-headline">
                            <span
                                class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[10px] font-bold mr-2">2</span>
                            Servicios
                        </h3>
                        @if (count($selectedServices) > 0)
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">
                                {{ count($selectedServices) }} seleccionado(s)
                            </span>
                        @endif
                    </div>

                    <div class="p-5">
                        @if (empty($services))
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <span
                                    class="material-symbols-outlined text-on-surface-variant/40 text-4xl mb-2">design_services</span>
                                <p class="text-sm text-on-surface-variant">Sin servicios disponibles.</p>
                            </div>
                        @else
                            @if (!$combinacionValida && $combinacionError)
                                <div
                                    class="mb-4 flex items-start gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800">
                                    <span
                                        class="material-symbols-outlined text-red-500 text-[18px] mt-0.5 flex-shrink-0">warning</span>
                                    <p class="text-xs">{{ $combinacionError }}</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($services as $service)
                                    @php $isSelected = in_array($service['id'], $selectedServices); @endphp
                                    <button wire:click="toggleServicio({{ $service['id'] }})"
                                        class="group relative text-left rounded-xl border-2 overflow-hidden transition-all duration-200
                                       {{ $isSelected ? 'border-primary shadow-md bg-primary/5' : 'border-outline-variant/20 hover:border-primary/40 hover:-translate-y-0.5 bg-surface-container-lowest' }}">
                                        <div
                                            class="absolute top-2.5 right-2.5 z-10 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all
                                            {{ $isSelected ? 'bg-primary border-primary' : 'border-outline-variant/50 bg-surface' }}">
                                            @if ($isSelected)
                                                <span
                                                    class="material-symbols-outlined text-white text-[12px]">check</span>
                                            @endif
                                        </div>
                                        <div class="w-full h-24 overflow-hidden bg-surface-container">
                                            @if ($service['image'])
                                                <img src="{{ $service['image'] }}" alt="{{ $service['name'] }}"
                                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                            @else
                                                <div
                                                    class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/5 flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-primary/30"
                                                        style="font-size:32px">spa</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-3 flex flex-col gap-1.5">
                                            <p
                                                class="text-sm font-bold text-on-surface line-clamp-1 {{ $isSelected ? 'text-primary' : 'group-hover:text-primary' }} transition-colors">
                                                {{ $service['name'] }}
                                            </p>
                                            <div class="flex gap-1.5 flex-wrap">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-primary-fixed text-on-primary-fixed text-[11px] font-semibold">
                                                    <span class="material-symbols-outlined text-[11px]">schedule</span>
                                                    {{ $service['duration'] }} min
                                                </span>
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-secondary-fixed text-on-secondary-fixed text-[11px] font-semibold">
                                                    <span class="material-symbols-outlined text-[11px]">payments</span>
                                                    ${{ number_format($service['price'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── PASO 3: FECHA Y HORA ─────────────────────────────── --}}
                @if (count($selectedServices) > 0 && $combinacionValida)
                    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden"
                        id="emp-datepicker-card">
                        <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-on-surface font-headline">
                                    <span
                                        class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[10px] font-bold mr-2">3</span>
                                    Fecha y hora
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-0.5 ml-7">Selecciona el día y luego el
                                    horario</p>
                            </div>
                            <div class="hidden sm:flex items-center gap-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    <span class="text-[11px] text-on-surface-variant">Disponible</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                                    <span class="text-[11px] text-on-surface-variant">Últimos slots</span>
                                </div>
                            </div>
                        </div>

                        @if ($fecha && $hora && !$esEmpleado)
                            <div class="px-6 pt-4">
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-primary/8 border border-primary/20">
                                    <span
                                        class="material-symbols-outlined text-primary text-[15px]">event_available</span>
                                    <span class="text-xs font-bold text-primary">
                                        {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM') }}
                                    </span>
                                    <span class="text-xs text-on-surface-variant">·</span>
                                    <span class="text-xs font-semibold text-on-surface">{{ $hora }} →
                                        {{ $horaFin }}</span>
                                </div>
                            </div>
                        @endif

                        <div wire:ignore>
                            <div class="flex items-center justify-between px-6 pt-5 pb-2">
                                <button id="emp-dp-prev"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition text-on-surface-variant hover:text-primary disabled:opacity-30 disabled:cursor-not-allowed">
                                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                                </button>
                                <div class="flex-1">
                                    <p id="emp-dp-month-left" class="text-sm font-bold text-on-surface text-center">
                                    </p>
                                </div>
                                <div id="emp-dp-loading" class="hidden w-8 h-8 items-center justify-center">
                                    <span
                                        class="animate-spin inline-block w-4 h-4 border-2 border-primary border-t-transparent rounded-full"></span>
                                </div>
                                <button id="emp-dp-next"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition text-on-surface-variant hover:text-primary">
                                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                                </button>
                            </div>

                            <div class="px-5 pb-5 grid grid-cols-1 md:grid-cols-2 gap-5 md:items-start">
                                <div>
                                    <div class="grid grid-cols-7 mb-1">
                                        @foreach (['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa', 'Do'] as $d)
                                            <div
                                                class="text-center text-[11px] font-semibold text-on-surface-variant/60 py-1">
                                                {{ $d }}</div>
                                        @endforeach
                                    </div>
                                    <div id="emp-dp-grid-left" class="grid grid-cols-7 gap-y-1"></div>
                                </div>
                                <div id="emp-dp-slots-inline" class="md:block">
                                    <div id="emp-dp-slots-inline-title"
                                        class="text-xs font-bold text-on-surface mb-3 min-h-[1.25rem]"></div>
                                    <div id="emp-dp-slots-inline-loading" class="hidden mb-2">
                                        <span
                                            class="animate-spin inline-block w-4 h-4 border-2 border-primary border-t-transparent rounded-full"></span>
                                    </div>
                                    <div id="emp-dp-slots-inline-grid" class="flex flex-wrap gap-2"></div>
                                    <div id="emp-dp-slots-inline-empty"
                                        class="hidden text-xs text-on-surface-variant text-center py-4">
                                        Sin horarios disponibles este día. Elige otro.
                                    </div>
                                    <div id="emp-dp-slots-inline-placeholder"
                                        class="flex flex-col items-center justify-center text-center py-8">
                                        <span
                                            class="material-symbols-outlined text-on-surface-variant/30 text-3xl mb-2">schedule</span>
                                        <p class="text-xs text-on-surface-variant">Selecciona un día<br>para ver
                                            horarios</p>
                                    </div>
                                </div>
                            </div>

                            <div id="emp-dp-slots-wrapper"
                                class="hidden px-5 pb-5 border-t border-outline-variant/20 pt-4">
                                <div class="flex items-center justify-between mb-3">
                                    <p id="emp-dp-slots-title" class="text-xs font-bold text-on-surface"></p>
                                    <div id="emp-dp-slots-loading" class="hidden">
                                        <span
                                            class="animate-spin inline-block w-4 h-4 border-2 border-primary border-t-transparent rounded-full"></span>
                                    </div>
                                </div>
                                <div id="emp-dp-slots-grid" class="flex flex-wrap gap-2"></div>
                                <div id="emp-dp-slots-empty"
                                    class="hidden text-xs text-on-surface-variant text-center py-4">
                                    Sin horarios disponibles este día. Elige otro.
                                </div>
                            </div>
                        </div>

                        <div id="emp-dp-data" data-company="{{ $companyId }}"
                            data-services="{{ implode(',', $selectedServices) }}"
                            data-total-duration="{{ collect($services)->whereIn('id', $selectedServices)->sum('duration') ?: 30 }}"
                            data-empleado="{{ $esEmpleado ? $empleadoUserId : '' }}" class="hidden">
                        </div>
                    </div>
                @endif

                {{-- ── PASO 4: PROFESIONALES ────────────────────────────── --}}
                @if ($fecha && $hora)
                    <div data-paso-emp="4"
                        class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-outline-variant/20">
                            <h3 class="text-sm font-bold text-on-surface font-headline">
                                <span
                                    class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[10px] font-bold mr-2">4</span>
                                Profesionales
                            </h3>
                        </div>

                        <div class="p-5 space-y-6" wire:loading.class="opacity-60 pointer-events-none"
                            wire:target="seleccionarSlot">
                            @if ($loadingProfesionales)
                                <div class="flex flex-col items-center justify-center py-8">
                                    <div
                                        class="animate-spin w-6 h-6 border-2 border-primary border-t-transparent rounded-full mb-2">
                                    </div>
                                    <p class="text-xs text-on-surface-variant">Buscando disponibilidad...</p>
                                </div>
                            @elseif(empty($profesionalesPorServicio))
                                <div class="flex flex-col items-center justify-center py-8 text-center">
                                    <span
                                        class="material-symbols-outlined text-error/50 text-4xl mb-2">event_busy</span>
                                    <p class="text-sm font-semibold text-on-surface mb-1">Sin disponibilidad</p>
                                    <p class="text-xs text-on-surface-variant">No hay profesionales disponibles en este
                                        horario.</p>
                                </div>
                            @else
                                @foreach ($profesionalesPorServicio as $serviceId => $grupo)
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-on-surface">
                                                    {{ $grupo['service']['name'] }}</p>
                                                <p class="text-xs text-on-surface-variant">
                                                    {{ $grupo['hora_inicio'] }} → {{ $grupo['hora_fin'] }} ·
                                                    {{ $grupo['service']['duration'] }} min
                                                </p>
                                            </div>
                                            @if (isset($selectedProfesionales[$serviceId]))
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-green-100 text-green-700 text-[11px] font-semibold">
                                                    <span
                                                        class="material-symbols-outlined text-[12px]">check_circle</span>
                                                    Asignado
                                                </span>
                                            @endif
                                        </div>

                                        @if (empty($grupo['profesionales']))
                                            <div
                                                class="flex items-center gap-2 px-4 py-3 rounded-lg bg-red-50 border border-red-200">
                                                <span
                                                    class="material-symbols-outlined text-red-500 text-[16px]">person_off</span>
                                                <p class="text-xs text-red-700">No hay profesionales disponibles para
                                                    este servicio en el horario seleccionado.</p>
                                            </div>
                                        @else
                                            <div class="flex flex-wrap gap-3">
                                                @foreach ($grupo['profesionales'] as $prof)
                                                    @php $isSelected = isset($selectedProfesionales[$serviceId]) && $selectedProfesionales[$serviceId] == $prof['id']; @endphp
                                                    <button
                                                        wire:click="seleccionarProfesional({{ $serviceId }}, {{ $prof['id'] }})"
                                                        class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all duration-200
                                           {{ $isSelected ? 'border-primary bg-primary/5 shadow-sm' : 'border-outline-variant/20 hover:border-primary/40 bg-surface-container' }}">
                                                        <div
                                                            class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 bg-primary/10 flex items-center justify-center">
                                                            @if ($prof['image'])
                                                                <img src="{{ $prof['image'] }}"
                                                                    alt="{{ $prof['name'] }}"
                                                                    class="w-full h-full object-cover">
                                                            @else
                                                                <span
                                                                    class="text-sm font-bold text-primary/60">{{ strtoupper(substr($prof['name'], 0, 1)) }}</span>
                                                            @endif
                                                        </div>
                                                        <span
                                                            class="text-sm font-semibold {{ $isSelected ? 'text-primary' : 'text-on-surface' }}">{{ $prof['name'] }}</span>
                                                        @if ($isSelected)
                                                            <span
                                                                class="material-symbols-outlined text-primary text-[16px]">check_circle</span>
                                                        @endif
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── PASO 5: NOTAS + CONFIRMAR ────────────────────────── --}}
                @if ($fecha && $hora && !empty($selectedProfesionales))
                    <div
                        class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-outline-variant/20">
                            <h3 class="text-sm font-bold text-on-surface font-headline">
                                <span
                                    class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-outline-variant text-on-surface-variant text-[10px] font-bold mr-2">5</span>
                                Notas adicionales
                                <span class="text-xs font-normal text-on-surface-variant ml-2">(Opcional)</span>
                            </h3>
                        </div>
                        <div class="p-5">
                            <textarea wire:model="notas" rows="3" placeholder="Observaciones, preferencias del cliente..."
                                class="w-full px-3 py-2.5 rounded-lg bg-surface-container border border-outline-variant/30
                                   text-sm text-on-surface placeholder:text-on-surface-variant/50
                                   focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition resize-none">
                        </textarea>
                        </div>
                    </div>

                    @php
                        $todosAsignados =
                            count($selectedProfesionales) === count($selectedServices) && count($selectedServices) > 0;
                    @endphp
                    <button wire:click="confirmar" wire:loading.attr="disabled" wire:target="confirmar"
                        @if (!$todosAsignados || !$clienteId) disabled @endif
                        class="w-full py-3.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center justify-center gap-2
                           {{ $todosAsignados && $clienteId ? 'bg-primary text-white hover:bg-primary/90 cursor-pointer' : 'bg-primary/30 text-white/60 cursor-not-allowed' }}">
                        <span wire:loading.remove wire:target="confirmar" class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">event_available</span>
                            Confirmar cita
                        </span>
                        <span wire:loading wire:target="confirmar" class="flex items-center gap-2">
                            <span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                            Confirmando...
                        </span>
                    </button>
                @endif

            </div>{{-- fin columna principal --}}

            {{-- ══════════════════════════════════════════
                 COLUMNA DERECHA — CITAS PRÓXIMAS DEL CLIENTE
            ══════════════════════════════════════════ --}}
            <div class="space-y-4">
                <div
                    class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm sticky top-4">
                    <div class="px-5 py-4 border-b border-outline-variant/20">
                        <h3 class="text-sm font-bold text-on-surface font-headline">
                            @if ($clienteData)
                                Citas de {{ $clienteData['name'] }}
                            @else
                                Citas próximas del cliente
                            @endif
                        </h3>
                        @if ($clienteData)
                            <p class="text-xs text-on-surface-variant mt-0.5">En esta empresa</p>
                        @endif
                    </div>

                    <div class="p-4 space-y-3">
                        @if (!$clienteData)
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <span
                                    class="material-symbols-outlined text-on-surface-variant/30 text-4xl mb-2">person_search</span>
                                <p class="text-xs text-on-surface-variant">Selecciona un cliente para ver sus citas</p>
                            </div>
                        @elseif(empty($proximasCita))
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <span
                                    class="material-symbols-outlined text-on-surface-variant/30 text-4xl mb-2">event_note</span>
                                <p class="text-xs text-on-surface-variant">Sin citas próximas</p>
                            </div>
                        @else
                            @foreach ($proximasCita as $cita)
                                <div class="bg-surface-container rounded-xl p-3.5 border border-outline-variant/10">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <p class="text-xs font-bold text-on-surface line-clamp-1">
                                            {{ $cita['servicios'] }}</p>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold flex-shrink-0
                                    {{ $cita['status'] === 'confirmed' ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $cita['status'] === 'confirmed' ? 'Confirmada' : 'Pendiente' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-1">
                                        <span
                                            class="material-symbols-outlined text-[13px] text-primary/60">calendar_today</span>
                                        <span>{{ $cita['fecha'] }}</span>
                                        <span class="text-outline-variant">·</span>
                                        <span
                                            class="material-symbols-outlined text-[13px] text-primary/60">schedule</span>
                                        <span>{{ $cita['hora'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                                        <span
                                            class="material-symbols-outlined text-[13px] text-primary/60">person</span>
                                        <span>{{ $cita['profesional'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    @script
        <script>
            (() => {
                let currentYear = new Date().getFullYear();
                let currentMonth = new Date().getMonth();
                let availability = {};
                let slotsCache = {};
                let selectedDate = null;
                let companyId = null;
                let serviceIds = [];
                let loadingRange = false;
                let empleadoId = null;

                const DAYS_AHEAD = 60;
                const pad = n => String(n).padStart(2, '0');
                const ymd = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
                const today = ymd(new Date());
                const MONTHS_ES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
                    'Octubre', 'Noviembre', 'Diciembre'
                ];

                function readParams() {
                    const el = document.getElementById('emp-dp-data');
                    if (!el) return false;
                    companyId = el.dataset.company;
                    serviceIds = el.dataset.services ? el.dataset.services.split(',').filter(Boolean) : [];
                    empleadoId = el.dataset.empleado || null;
                    return companyId && serviceIds.length > 0;
                }

                async function loadAvailability() {
                    if (!readParams() || loadingRange) return;
                    loadingRange = true;
                    document.getElementById('emp-dp-loading')?.classList.remove('hidden');
                    document.getElementById('emp-dp-loading')?.classList.add('flex');

                    const start = new Date();
                    const end = new Date();
                    end.setDate(end.getDate() + DAYS_AHEAD);

                    const params = new URLSearchParams({
                        company_id: companyId,
                        start: start.toISOString(),
                        end: end.toISOString()
                    });
                    serviceIds.forEach(id => params.append('services[]', id));
                    if (empleadoId) params.append('empleado_id', empleadoId);

                    try {
                        const res = await fetch(`/booking/citas-ocupadas?${params}`);
                        const data = await res.json();

                        availability = {};
                        slotsCache = {};

                        (data.disponibles || []).forEach(d => {
                            const key = d.fecha;
                            const slotDateTime = new Date(`${d.fecha}T${d.inicio}`);
                            if (slotDateTime <= new Date()) return;

                            if (!slotsCache[key]) slotsCache[key] = [];
                            slotsCache[key].push(d);

                            if (!availability[key]) {
                                availability[key] = (d.disponibles === 1 && serviceIds.length === 1) ? 'low' :
                                    'available';
                            }
                            if (d.disponibles > 1) availability[key] = 'available';
                        });

                        (data.citas || []).forEach(c => {
                            if (!availability[c.fecha]) availability[c.fecha] = 'full';
                        });

                        renderCalendar();
                    } catch (e) {
                        console.error('Error cargando disponibilidad', e);
                    } finally {
                        loadingRange = false;
                        document.getElementById('emp-dp-loading')?.classList.add('hidden');
                        document.getElementById('emp-dp-loading')?.classList.remove('flex');
                    }
                }

                function renderMonth(gridEl, year, month) {
                    if (!gridEl) return;
                    gridEl.innerHTML = '';
                    const firstDay = new Date(year, month, 1);
                    let startOffset = firstDay.getDay() - 1;
                    if (startOffset < 0) startOffset = 6;
                    const daysInMonth = new Date(year, month + 1, 0).getDate();

                    for (let i = 0; i < startOffset; i++) gridEl.insertAdjacentHTML('beforeend', '<div></div>');

                    for (let day = 1; day <= daysInMonth; day++) {
                        const d = new Date(year, month, day);
                        const key = ymd(d);
                        const isPast = key < today;
                        const state = availability[key];

                        let btnClass =
                            'relative flex flex-col items-center justify-center w-full aspect-square rounded-xl text-[13px] font-semibold transition-all duration-150 ';
                        let dotHtml = '';
                        let disabled = false;

                        if (isPast) {
                            btnClass += 'text-on-surface-variant/30 cursor-not-allowed';
                            disabled = true;
                        } else if (state === 'available') {
                            btnClass += key === selectedDate ? 'bg-primary text-white shadow-md scale-105' :
                                'hover:bg-primary/10 hover:text-primary text-on-surface cursor-pointer';
                            dotHtml =
                                `<span class="w-1.5 h-1.5 rounded-full mt-0.5 ${key === selectedDate ? 'bg-white' : 'bg-emerald-400'}"></span>`;
                        } else if (state === 'low') {
                            btnClass += key === selectedDate ? 'bg-primary text-white shadow-md scale-105' :
                                'hover:bg-orange-50 hover:text-orange-700 text-on-surface cursor-pointer';
                            dotHtml =
                                `<span class="w-1.5 h-1.5 rounded-full mt-0.5 ${key === selectedDate ? 'bg-white' : 'bg-orange-400'}"></span>`;
                        } else {
                            btnClass += 'text-on-surface-variant/40 cursor-not-allowed';
                            disabled = true;
                        }

                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.disabled = disabled;
                        btn.className = btnClass;
                        btn.innerHTML = `<span>${day}</span>${dotHtml}`;
                        if (!disabled) btn.addEventListener('click', () => selectDay(key));
                        gridEl.appendChild(btn);
                    }
                }

                function renderCalendar() {
                    const leftGrid = document.getElementById('emp-dp-grid-left');
                    const leftLabel = document.getElementById('emp-dp-month-left');
                    const prevBtn = document.getElementById('emp-dp-prev');
                    if (!leftGrid) return;

                    if (leftLabel) leftLabel.textContent = `${MONTHS_ES[currentMonth]} ${currentYear}`;

                    const now = new Date();
                    if (prevBtn) prevBtn.disabled = (currentYear === now.getFullYear() && currentMonth <= now.getMonth());

                    renderMonth(leftGrid, currentYear, currentMonth);
                }

                async function selectDay(dateKey) {
                    if (selectedDate && selectedDate !== dateKey) {
                        $wire.set('fecha', null);
                        $wire.set('hora', null);
                        $wire.set('horaFin', null);
                        $wire.set('selectedProfesionales', {});
                        $wire.set('profesionalesPorServicio', {});
                        document.querySelectorAll('.emp-slot-pill').forEach(p => {
                            p.classList.remove('border-primary', 'bg-primary', 'text-white', 'shadow-md');
                            p.classList.add('border-outline-variant/30', 'bg-surface-container',
                                'text-on-surface');
                        });
                    }

                    selectedDate = dateKey;
                    renderCalendar();

                    const placeholder = document.getElementById('emp-dp-slots-inline-placeholder');
                    const title = document.getElementById('emp-dp-slots-inline-title');
                    const grid = document.getElementById('emp-dp-slots-inline-grid');
                    const empty = document.getElementById('emp-dp-slots-inline-empty');
                    const loading = document.getElementById('emp-dp-slots-inline-loading');
                    if (!grid) return;

                    if (placeholder) placeholder.classList.add('hidden');
                    grid.innerHTML = '';
                    empty.classList.add('hidden');
                    loading.classList.remove('hidden');

                    const d = new Date(dateKey + 'T12:00:00');
                    const DAYS_ES = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
                    if (title) title.textContent =
                        `${DAYS_ES[d.getDay()]} ${d.getDate()} de ${MONTHS_ES[d.getMonth()]}`;

                    try {
                        let disponibles;
                        if (slotsCache[dateKey]) {
                            disponibles = slotsCache[dateKey];
                            loading.classList.add('hidden');
                        } else {
                            const params = new URLSearchParams({
                                company_id: companyId,
                                start: new Date(dateKey + 'T00:00:00').toISOString(),
                                end: new Date(dateKey + 'T23:59:59').toISOString()
                            });
                            serviceIds.forEach(id => params.append('services[]', id));
                            const res = await fetch(`/booking/citas-ocupadas?${params}`);
                            const data = await res.json();
                            disponibles = data.disponibles || [];
                            loading.classList.add('hidden');
                        }

                        const ahora = new Date();
                        const slotsDisponibles = disponibles
                            .filter(s => s.fecha === dateKey && s.disponibles > 0 && new Date(
                                `${s.fecha}T${s.inicio}`) > ahora)
                            .sort((a, b) => a.inicio.localeCompare(b.inicio));

                        if (slotsDisponibles.length === 0) {
                            empty.classList.remove('hidden');
                            return;
                        }

                        slotsDisponibles.forEach(slot => {
                            const pill = document.createElement('button');
                            pill.type = 'button';
                            pill.className =
                                'emp-slot-pill px-4 py-2 rounded-xl text-sm font-semibold border-2 transition-all duration-150 border-outline-variant/30 bg-surface-container hover:border-primary/50 hover:bg-primary/5 hover:text-primary text-on-surface';
                            pill.textContent = slot.inicio;
                            if (slot.disponibles === 1 && serviceIds.length === 1) {
                                pill.insertAdjacentHTML('beforeend',
                                    '<span class="ml-1.5 text-[10px] text-orange-500 font-bold">· Última Cita</span>'
                                );
                            }
                            pill.addEventListener('click', () => selectSlot(dateKey, slot.inicio, pill));
                            grid.appendChild(pill);
                        });

                    } catch (e) {
                        loading.classList.add('hidden');
                        empty.classList.remove('hidden');
                        console.error(e);
                    }
                }

                function selectSlot(fecha, hora, pillEl) {
                    document.querySelectorAll('.emp-slot-pill').forEach(p => {
                        p.classList.remove('border-primary', 'bg-primary', 'text-white', 'shadow-md');
                        p.classList.add('border-outline-variant/30', 'bg-surface-container', 'text-on-surface');
                    });
                    pillEl.classList.remove('border-outline-variant/30', 'bg-surface-container', 'text-on-surface');
                    pillEl.classList.add('border-primary', 'bg-primary', 'text-white', 'shadow-md');

                    $wire.call('seleccionarSlot', fecha, hora);

                    setTimeout(() => {
                        const paso4 = document.querySelector('[data-paso-emp="4"]');
                        if (paso4) paso4.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 300);
                }

                function setupNav() {
                    document.getElementById('emp-dp-prev')?.addEventListener('click', () => {
                        const now = new Date();
                        if (currentYear === now.getFullYear() && currentMonth <= now.getMonth()) return;
                        if (currentMonth === 0) {
                            currentMonth = 11;
                            currentYear--;
                        } else currentMonth--;
                        renderCalendar();
                    });
                    document.getElementById('emp-dp-next')?.addEventListener('click', () => {
                        if (currentMonth === 11) {
                            currentMonth = 0;
                            currentYear++;
                        } else currentMonth++;
                        renderCalendar();
                    });
                }

                function init() {
                    if (!readParams()) return;
                    const now = new Date();
                    currentYear = now.getFullYear();
                    currentMonth = now.getMonth();
                    selectedDate = null;
                    availability = {};
                    slotsCache = {};

                    const placeholder = document.getElementById('emp-dp-slots-inline-placeholder');
                    if (placeholder) placeholder.classList.remove('hidden');
                    const inlineGrid = document.getElementById('emp-dp-slots-inline-grid');
                    if (inlineGrid) inlineGrid.innerHTML = '';
                    const inlineTitle = document.getElementById('emp-dp-slots-inline-title');
                    if (inlineTitle) inlineTitle.textContent = '';

                    setupNav();
                    renderCalendar();
                    loadAvailability();
                }

                document.addEventListener('livewire:initialized', () => {
                    if (document.getElementById('emp-dp-data')) init();
                });

                $wire.on('emp-servicios-actualizados', () => setTimeout(init, 80));
                $wire.on('emp-reiniciar-calendario', () => {
                    selectedDate = null;
                    availability = {};
                    slotsCache = {};
                    loadingRange = false;
                    const placeholder = document.getElementById('emp-dp-slots-inline-placeholder');
                    if (placeholder) placeholder.classList.remove('hidden');
                    const inlineGrid = document.getElementById('emp-dp-slots-inline-grid');
                    if (inlineGrid) inlineGrid.innerHTML = '';
                    const inlineTitle = document.getElementById('emp-dp-slots-inline-title');
                    if (inlineTitle) inlineTitle.textContent = '';
                    renderCalendar();
                    loadAvailability();
                });

            })();
        </script>
    @endscript
</div>
