<div>
    {{-- LOADING BAR --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- ── SUCCESS TOAST ──────────────────────────────────────────────── --}}
    @if($successMsg)
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3
               bg-green-600 text-white text-sm font-medium px-5 py-3.5 rounded-2xl shadow-xl max-w-sm w-full mx-4">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        <span class="flex-1">{{ $successMsg }}</span>
        <button @click="show = false"><span class="material-symbols-outlined text-[18px]">close</span></button>
    </div>
    @endif

    {{-- ── ERROR TOAST ─────────────────────────────────────────────────── --}}
    @if($errorMsg)
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3
               bg-red-600 text-white text-sm font-medium px-5 py-3.5 rounded-2xl shadow-xl max-w-sm w-full mx-4">
        <span class="material-symbols-outlined text-[18px]">error</span>
        <span class="flex-1">{{ $errorMsg }}</span>
        <button @click="show = false"><span class="material-symbols-outlined text-[18px]">close</span></button>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         LAYOUT PRINCIPAL
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="p-6 pb-24">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ══════════════════════════════════════════
                 COLUMNA PRINCIPAL — FORMULARIO
            ══════════════════════════════════════════ --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- ── PASO 1: EMPRESA ──────────────────────────────────── --}}
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-on-surface font-headline">
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[10px] font-bold mr-2">1</span>
                                Empresa
                            </h3>
                        </div>
                        @if($companyData)
                        <button wire:click="$set('companyId', null); $set('companyData', null); $set('services', []); $set('selectedServices', [])"
                            class="text-xs text-on-surface-variant hover:text-primary transition flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">edit</span>
                            Cambiar
                        </button>
                        @endif
                    </div>

                    @if($companyData)
                    {{-- Empresa seleccionada (bloqueada) --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-primary/10 flex items-center justify-center border border-outline-variant/20">
                            @if($companyData['logo'])
                            <img src="{{ $companyData['logo'] }}" alt="{{ $companyData['name'] }}" class="w-full h-full object-cover">
                            @else
                            <span class="text-sm font-bold text-primary/50">{{ strtoupper(substr($companyData['name'], 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-on-surface">{{ $companyData['name'] }}</p>
                            <div class="flex flex-wrap gap-3 mt-0.5">
                                @if($companyData['address'])
                                <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[13px] text-primary/60">location_on</span>
                                    {{ $companyData['address'] }}
                                </span>
                                @endif
                                @if($companyData['phone'])
                                <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[13px] text-primary/60">call</span>
                                    {{ $companyData['phone'] }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-green-100 text-green-700 text-[11px] font-semibold">
                            <span class="material-symbols-outlined text-[12px]">check_circle</span>
                            Seleccionada
                        </span>
                    </div>
                    @else
                    {{-- Grid de cards de empresas --}}
                    <div class="p-5 space-y-6">
                        @foreach($tiposNegocio as $tipo)
                        @if($tipo->companies->count() > 0)
                        <div>
                            <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide mb-3">{{ $tipo->name }}</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($tipo->companies as $company)
                                <button wire:click="seleccionarEmpresa({{ $company->id }})"
                                    class="group text-left bg-surface-container rounded-xl border border-outline-variant/20
                                           hover:border-primary/40 hover:shadow-md transition-all duration-200 overflow-hidden">
                                    <div class="flex items-center gap-3 p-3">
                                        <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-primary/10 flex items-center justify-center">
                                            @if($company->logo)
                                            <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                                            @else
                                            <span class="text-xs font-bold text-primary/50">{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-on-surface group-hover:text-primary transition-colors line-clamp-1">
                                                {{ $company->name }}
                                            </p>
                                            <p class="text-xs text-on-surface-variant line-clamp-1">{{ $company->address }}</p>
                                        </div>
                                        <span class="material-symbols-outlined text-on-surface-variant/40 group-hover:text-primary transition-colors text-[18px]">
                                            chevron_right
                                        </span>
                                    </div>
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- ── PASO 2: SERVICIOS ────────────────────────────────── --}}
                @if($companyData)
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden"
                    wire:loading.class="opacity-60 pointer-events-none" wire:target="seleccionarEmpresa">
                    <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-on-surface font-headline">
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[10px] font-bold mr-2">2</span>
                                Servicios
                            </h3>
                            <p class="text-xs text-on-surface-variant mt-0.5 ml-7">Puedes seleccionar uno o varios</p>
                        </div>
                        @if(count($selectedServices) > 0)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">
                            {{ count($selectedServices) }} seleccionado(s)
                        </span>
                        @endif
                    </div>

                    <div class="p-5">
                        @if(empty($services))
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <span class="material-symbols-outlined text-on-surface-variant/40 text-4xl mb-2">design_services</span>
                            <p class="text-sm text-on-surface-variant">Sin servicios disponibles para esta empresa.</p>
                        </div>
                        @else

                        {{-- Banner combinación inválida --}}
                        @if(!$combinacionValida && $combinacionError)
                        <div class="mb-4 flex items-start gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800">
                            <span class="material-symbols-outlined text-red-500 text-[18px] mt-0.5 flex-shrink-0">warning</span>
                            <p class="text-xs">{{ $combinacionError }}</p>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($services as $service)
                            @php $isSelected = in_array($service['id'], $selectedServices); @endphp
                            <button wire:click="toggleServicio({{ $service['id'] }})"
                                class="group relative text-left rounded-xl border-2 overflow-hidden transition-all duration-200
                                       {{ $isSelected
                                           ? 'border-primary shadow-md bg-primary/5'
                                           : 'border-outline-variant/20 hover:border-primary/40 hover:-translate-y-0.5 bg-surface-container-lowest' }}">

                                {{-- Check indicator --}}
                                <div class="absolute top-2.5 right-2.5 z-10 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all
                                            {{ $isSelected ? 'bg-primary border-primary' : 'border-outline-variant/50 bg-surface' }}">
                                    @if($isSelected)
                                    <span class="material-symbols-outlined text-white text-[12px]">check</span>
                                    @endif
                                </div>

                                {{-- Imagen --}}
                                <div class="w-full h-28 overflow-hidden bg-surface-container">
                                    @if($service['image'])
                                    <img src="{{ $service['image'] }}" alt="{{ $service['name'] }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/5 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary/30" style="font-size:36px">spa</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="p-3 flex flex-col gap-1.5">
                                    <p class="text-sm font-bold text-on-surface line-clamp-1 {{ $isSelected ? 'text-primary' : 'group-hover:text-primary' }} transition-colors">
                                        {{ $service['name'] }}
                                    </p>
                                    <div class="flex gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-primary-fixed text-on-primary-fixed text-[11px] font-semibold">
                                            <span class="material-symbols-outlined text-[11px]">schedule</span>
                                            {{ $service['duration'] }} min
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-secondary-fixed text-on-secondary-fixed text-[11px] font-semibold">
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
                @endif

                {{-- ── PASO 3: FECHA Y HORA ─────────────────────────────── --}}
                @if($companyData && count($selectedServices) > 0 && $combinacionValida)
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden"
                    id="datepicker-card">
                    <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-on-surface font-headline">
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[10px] font-bold mr-2">3</span>
                                Fecha y hora
                            </h3>
                            <p class="text-xs text-on-surface-variant mt-0.5 ml-7">Selecciona el día y luego el horario</p>
                        </div>
                        {{-- Leyenda compacta --}}
                        <div class="hidden sm:flex items-center gap-4">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span class="text-[11px] text-on-surface-variant">Disponible</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                                <span class="text-[11px] text-on-surface-variant">Últimos slots</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-surface-variant border border-outline-variant/30"></span>
                                <span class="text-[11px] text-on-surface-variant">Sin disponibilidad</span>
                            </div>
                        </div>
                    </div>

                    {{-- Resumen del slot seleccionado --}}
                    @if($fecha && $hora)
                    <div class="px-6 pt-4">
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-primary/8 border border-primary/20">
                            <span class="material-symbols-outlined text-primary text-[15px]">event_available</span>
                            <span class="text-xs font-bold text-primary">
                                {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM') }}
                            </span>
                            <span class="text-xs text-on-surface-variant">·</span>
                            <span class="text-xs font-semibold text-on-surface">{{ $hora }} → {{ $horaFin }}</span>
                        </div>
                    </div>
                    @endif

                    {{-- Picker container: controlado 100% por JS, wire:ignore para que Livewire no lo toque --}}
                    <div wire:ignore>
                        {{-- Navegación de meses --}}
                        <div class="flex items-center justify-between px-6 pt-5 pb-2">
                            <button id="dp-prev"
                                class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition text-on-surface-variant hover:text-primary disabled:opacity-30 disabled:cursor-not-allowed">
                                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            </button>
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-0">
                                <p id="dp-month-left" class="text-sm font-bold text-on-surface text-center"></p>
                                <p id="dp-month-right" class="text-sm font-bold text-on-surface text-center hidden md:block"></p>
                            </div>
                            {{-- Spinner de carga --}}
                            <div id="dp-loading" class="hidden w-8 h-8 items-center justify-center">
                                <span class="animate-spin inline-block w-4 h-4 border-2 border-primary border-t-transparent rounded-full"></span>
                            </div>
                            <button id="dp-next"
                                class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition text-on-surface-variant hover:text-primary">
                                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </button>
                        </div>

                        {{-- Grids de días --}}
                        <div class="px-5 pb-3 grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Mes izquierdo --}}
                            <div>
                                <div class="grid grid-cols-7 mb-1">
                                    @foreach(['Lu','Ma','Mi','Ju','Vi','Sa','Do'] as $d)
                                    <div class="text-center text-[11px] font-semibold text-on-surface-variant/60 py-1">{{ $d }}</div>
                                    @endforeach
                                </div>
                                <div id="dp-grid-left" class="grid grid-cols-7 gap-y-1"></div>
                            </div>
                            {{-- Mes derecho (solo desktop) --}}
                            <div class="hidden md:block">
                                <div class="grid grid-cols-7 mb-1">
                                    @foreach(['Lu','Ma','Mi','Ju','Vi','Sa','Do'] as $d)
                                    <div class="text-center text-[11px] font-semibold text-on-surface-variant/60 py-1">{{ $d }}</div>
                                    @endforeach
                                </div>
                                <div id="dp-grid-right" class="grid grid-cols-7 gap-y-1"></div>
                            </div>
                        </div>

                        {{-- Pills de hora — se muestran al seleccionar día --}}
                        <div id="dp-slots-wrapper" class="hidden px-5 pb-5 border-t border-outline-variant/20 pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <p id="dp-slots-title" class="text-xs font-bold text-on-surface"></p>
                                <div id="dp-slots-loading" class="hidden">
                                    <span class="animate-spin inline-block w-4 h-4 border-2 border-primary border-t-transparent rounded-full"></span>
                                </div>
                            </div>
                            <div id="dp-slots-grid" class="flex flex-wrap gap-2"></div>
                            <div id="dp-slots-empty" class="hidden text-xs text-on-surface-variant text-center py-4">
                                Sin horarios disponibles este día. Elige otro.
                            </div>
                        </div>
                    </div>

                    {{-- data-attributes para que el JS lea los parámetros actuales --}}
                    <div id="dp-data"
                        data-company="{{ $companyId }}"
                        data-services="{{ implode(',', $selectedServices) }}"
                        data-hora-inicio="{{ $horaInicio }}"
                        data-hora-fin="{{ $horaFinEmpresa }}"
                        data-total-duration="{{ collect($services)->sum('duration') ?: 30 }}"
                        class="hidden">
                    </div>
                </div>
                @endif

                {{-- ── PASO 4: PROFESIONALES ────────────────────────────── --}}
                @if($fecha && $hora)
                <div data-paso="4" class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-outline-variant/20">
                        <h3 class="text-sm font-bold text-on-surface font-headline">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[10px] font-bold mr-2">4</span>
                            Profesionales
                        </h3>
                    </div>

                    {{-- Aviso informativo --}}
                    <div class="mx-6 mt-4 flex items-start gap-2 px-3 py-2.5 rounded-lg bg-blue-50 border border-blue-200">
                        <span class="material-symbols-outlined text-blue-500 text-[16px] mt-0.5 flex-shrink-0">info</span>
                        <p class="text-xs text-blue-800 leading-relaxed">
                            Solo aparecen empleados que pueden atender tu cita. Esto depende de su asignación al servicio y horario disponible de cada uno.
                        </p>
                    </div>

                    <div class="p-5 space-y-6"
                        wire:loading.class="opacity-60 pointer-events-none" wire:target="seleccionarSlot">

                        @if($loadingProfesionales)
                        <div class="flex flex-col items-center justify-center py-8">
                            <div class="animate-spin w-6 h-6 border-2 border-primary border-t-transparent rounded-full mb-2"></div>
                            <p class="text-xs text-on-surface-variant">Buscando disponibilidad...</p>
                        </div>
                        @elseif(empty($profesionalesPorServicio))
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <span class="material-symbols-outlined text-error/50 text-4xl mb-2">event_busy</span>
                            <p class="text-sm font-semibold text-on-surface mb-1">Sin disponibilidad</p>
                            <p class="text-xs text-on-surface-variant">No hay profesionales disponibles en este horario. Intenta otro slot.</p>
                        </div>
                        @else
                        @foreach($profesionalesPorServicio as $serviceId => $grupo)
                        <div>
                            {{-- Header del servicio --}}
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-on-surface">{{ $grupo['service']['name'] }}</p>
                                    <p class="text-xs text-on-surface-variant">
                                        {{ $grupo['hora_inicio'] }} → {{ $grupo['hora_fin'] }}
                                        · {{ $grupo['service']['duration'] }} min
                                    </p>
                                </div>
                                @if(isset($selectedProfesionales[$serviceId]))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-green-100 text-green-700 text-[11px] font-semibold">
                                    <span class="material-symbols-outlined text-[12px]">check_circle</span>
                                    Asignado
                                </span>
                                @endif
                            </div>

                            @if(empty($grupo['profesionales']))
                            <div class="flex items-center gap-2 px-4 py-3 rounded-lg bg-red-50 border border-red-200">
                                <span class="material-symbols-outlined text-red-500 text-[16px]">person_off</span>
                                <p class="text-xs text-red-700">No hay profesionales disponibles para este servicio en el horario seleccionado.</p>
                            </div>
                            @else
                            <div class="flex flex-wrap gap-3">
                                @foreach($grupo['profesionales'] as $prof)
                                @php $isSelected = isset($selectedProfesionales[$serviceId]) && $selectedProfesionales[$serviceId] == $prof['id']; @endphp
                                <button wire:click="seleccionarProfesional({{ $serviceId }}, {{ $prof['id'] }})"
                                    class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all duration-200
                                           {{ $isSelected
                                               ? 'border-primary bg-primary/5 shadow-sm'
                                               : 'border-outline-variant/20 hover:border-primary/40 bg-surface-container' }}">
                                    <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 bg-primary/10 flex items-center justify-center">
                                        @if($prof['image'])
                                        <img src="{{ $prof['image'] }}" alt="{{ $prof['name'] }}" class="w-full h-full object-cover">
                                        @else
                                        <span class="text-sm font-bold text-primary/60">{{ strtoupper(substr($prof['name'], 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold {{ $isSelected ? 'text-primary' : 'text-on-surface' }}">
                                        {{ $prof['name'] }}
                                    </span>
                                    @if($isSelected)
                                    <span class="material-symbols-outlined text-primary text-[16px]">check_circle</span>
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

                {{-- ── PASO 5: NOTAS ────────────────────────────────────── --}}
                @if($fecha && $hora && !empty($selectedProfesionales))
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-outline-variant/20">
                        <h3 class="text-sm font-bold text-on-surface font-headline">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-outline-variant text-on-surface-variant text-[10px] font-bold mr-2">5</span>
                            Notas adicionales
                            <span class="text-xs font-normal text-on-surface-variant ml-2">(Opcional)</span>
                        </h3>
                    </div>
                    <div class="p-5">
                        <textarea wire:model="notas" rows="3"
                            placeholder="Ej. Tengo alergia a ciertos productos, prefiero el lado izquierdo..."
                            class="w-full px-3 py-2.5 rounded-lg bg-surface-container border border-outline-variant/30
                                   text-sm text-on-surface placeholder:text-on-surface-variant/50
                                   focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition resize-none">
                        </textarea>
                    </div>
                </div>

                {{-- ── BOTÓN CONFIRMAR ──────────────────────────────────── --}}
                @php
                $todosAsignados = count($selectedProfesionales) === count($selectedServices)
                && count($selectedServices) > 0;
                @endphp
                <button wire:click="confirmar"
                    wire:loading.attr="disabled"
                    wire:target="confirmar"
                    @if(!$todosAsignados) disabled @endif
                    class="w-full py-3.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center justify-center gap-2
                           {{ $todosAsignados
                               ? 'bg-primary text-white hover:bg-primary/90 cursor-pointer'
                               : 'bg-primary/30 text-white/60 cursor-not-allowed' }}">
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
                 COLUMNA DERECHA — PRÓXIMAS CITAS
            ══════════════════════════════════════════ --}}
            <div class="space-y-4">
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm sticky top-4">
                    <div class="px-5 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-on-surface font-headline">Mis próximas citas</h3>
                        <a href="{{ route('appointment.history') }}"
                            class="text-xs text-primary hover:text-primary/70 transition font-semibold">
                            Ver todas
                        </a>
                    </div>

                    <div class="p-4 space-y-3">
                        @forelse($proximas as $cita)
                        <div wire:key="cita-{{ $cita['id'] }}"
                            x-data="{ removing: false }"
                            x-show="!removing"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            @cita-cancelada.window="if($event.detail.id === {{ $cita['id'] }}) removing = true"
                            class="bg-surface-container rounded-xl p-3.5 border border-outline-variant/10">

                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-on-surface line-clamp-1">{{ $cita['company'] }}</p>
                                    <p class="text-xs text-on-surface-variant line-clamp-1">{{ $cita['servicios'] }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold flex-shrink-0
                                    {{ $cita['status'] === 'confirmed' ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $cita['status'] === 'confirmed' ? 'Confirmada' : 'Pendiente' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-1">
                                <span class="material-symbols-outlined text-[13px] text-primary/60">calendar_today</span>
                                <span>{{ $cita['fecha'] }}</span>
                                <span class="text-outline-variant">·</span>
                                <span class="material-symbols-outlined text-[13px] text-primary/60">schedule</span>
                                <span>{{ $cita['hora'] }}</span>
                            </div>

                            <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-1">
                                <span class="material-symbols-outlined text-[13px] text-primary/60">person</span>
                                <span>{{ $cita['profesional'] }}</span>
                            </div>

                            @if(!empty($cita['direccion']))
                            <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-1">
                                <span class="material-symbols-outlined text-[13px] text-primary/60">location_on</span>
                                <span>{{ $cita['direccion'] }}</span>
                            </div>
                            @endif

                            @if(!empty($cita['telefono']))
                            <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-3">
                                <span class="material-symbols-outlined text-[13px] text-primary/60">call</span>
                                <span>{{ $cita['telefono'] }}</span>
                            </div>
                            @endif

                            @if($cita['cancellable'])
                            <button
                                x-on:click="
                                    Swal.fire({
                                        title: '¿Cancelar esta cita?',
                                        text: 'Esta acción no se puede deshacer.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Sí, cancelar',
                                        cancelButtonText: 'No, volver',
                                        confirmButtonColor: '#d33',
                                        background: '#fcf9f3',
                                        color: '#1c1c19'
                                    }).then(result => {
                                        if (result.isConfirmed) {
                                            $wire.cancelarCita({{ $cita['id'] }});
                                        }
                                    })
                                "
                                wire:loading.attr="disabled"
                                wire:target="cancelarCita({{ $cita['id'] }})"
                                class="w-full py-1.5 rounded-lg text-xs font-semibold border border-error/30 text-error
                                       hover:bg-error/5 transition flex items-center justify-center gap-1.5">
                                <span wire:loading.remove wire:target="cancelarCita({{ $cita['id'] }})">
                                    <span class="material-symbols-outlined text-[13px]">event_busy</span>
                                    Cancelar cita
                                </span>
                                <span wire:loading wire:target="cancelarCita({{ $cita['id'] }})">
                                    <span class="animate-spin inline-block w-3 h-3 border border-error border-t-transparent rounded-full"></span>
                                    Cancelando...
                                </span>
                            </button>
                            @else
                            <div class="w-full py-1.5 rounded-lg text-xs text-center text-on-surface-variant/50 border border-outline-variant/20 cursor-not-allowed"
                                title="No se puede cancelar con menos de 2 horas de anticipación">
                                No cancelable
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <span class="material-symbols-outlined text-on-surface-variant/30 text-4xl mb-2">event_note</span>
                            <p class="text-xs text-on-surface-variant">No tienes citas próximas</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>{{-- fin grid --}}
    </div>

    @script
    <script>
        (() => {
            // ── Estado del picker ────────────────────────────────────────────
            let currentYear = new Date().getFullYear();
            let currentMonth = new Date().getMonth(); // 0-indexed
            let availability = {};
            let slotsCache = {}; // { 'YYYY-MM-DD': [{fecha, inicio, fin, disponibles}] }
            let selectedDate = null;
            let companyId = null;
            let serviceIds = [];
            let loadingRange = false;

            const DAYS_AHEAD = 60; // cuántos días hacia adelante pre-cargar

            // ── Helpers de fecha ─────────────────────────────────────────────
            const pad = n => String(n).padStart(2, '0');
            const ymd = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
            const today = ymd(new Date());

            const MONTHS_ES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ];

            // ── Leer params del DOM ──────────────────────────────────────────
            function readParams() {
                const el = document.getElementById('dp-data');
                if (!el) return false;
                companyId = el.dataset.company;
                serviceIds = el.dataset.services ? el.dataset.services.split(',').filter(Boolean) : [];
                return companyId && serviceIds.length > 0;
            }

            // ── Cargar disponibilidad del servidor ───────────────────────────
            async function loadAvailability() {
                if (!readParams()) return;
                if (loadingRange) return;
                loadingRange = true;

                document.getElementById('dp-loading')?.classList.remove('hidden');

                const start = new Date();
                const end = new Date();
                end.setDate(end.getDate() + DAYS_AHEAD);

                const params = new URLSearchParams({
                    company_id: companyId,
                    start: start.toISOString(),
                    end: end.toISOString(),
                });
                serviceIds.forEach(id => params.append('services[]', id));

                try {
                    const res = await fetch(`/booking/citas-ocupadas?${params}`);
                    const data = await res.json();

                    // Resetear y reclasificar por día
                    availability = {};

                    // días con slots disponibles
                    (data.disponibles || []).forEach(d => {
                        const key = d.fecha;
                        const ahora = new Date();
                        const slotDateTime = new Date(`${d.fecha}T${d.inicio}`);
                        if (slotDateTime <= ahora) return;

                        // Guardar en cache para reusar al hacer clic en el día
                        if (!slotsCache[key]) slotsCache[key] = [];
                        slotsCache[key].push(d);

                        if (!availability[key]) {
                            availability[key] = (d.disponibles === 1 && serviceIds.length === 1) ? 'low' : 'available';
                        }
                        if (d.disponibles > 1) availability[key] = 'available';
                    });

                    // días completamente bloqueados (están en citas pero no en disponibles)
                    (data.citas || []).forEach(c => {
                        if (!availability[c.fecha]) availability[c.fecha] = 'full';
                    });

                    renderCalendar();
                } catch (e) {
                    console.error('Error cargando disponibilidad', e);
                } finally {
                    loadingRange = false;
                    document.getElementById('dp-loading')?.classList.add('hidden');
                }
            }

            // ── Render de un mes en un grid ───────────────────────────────────
            function renderMonth(gridEl, year, month) {
                if (!gridEl) return;
                gridEl.innerHTML = '';

                const firstDay = new Date(year, month, 1);
                // lunes=0 ... domingo=6
                let startOffset = firstDay.getDay() - 1;
                if (startOffset < 0) startOffset = 6;

                const daysInMonth = new Date(year, month + 1, 0).getDate();

                // Celdas vacías iniciales
                for (let i = 0; i < startOffset; i++) {
                    gridEl.insertAdjacentHTML('beforeend', '<div></div>');
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const d = new Date(year, month, day);
                    const key = ymd(d);
                    const isPast = key < today;
                    const state = availability[key]; // 'available'|'low'|'full'|undefined

                    let btnClass = 'relative flex flex-col items-center justify-center w-full aspect-square rounded-xl text-[13px] font-semibold transition-all duration-150 ';
                    let dotClass = 'w-1.5 h-1.5 rounded-full mt-0.5 ';
                    let disabled = false;
                    let title = '';

                    if (isPast) {
                        btnClass += 'text-on-surface-variant/30 cursor-not-allowed';
                        dotClass = '';
                        disabled = true;
                    } else if (state === 'available') {
                        btnClass += key === selectedDate ?
                            'bg-primary text-white shadow-md scale-105' :
                            'hover:bg-primary/10 hover:text-primary text-on-surface cursor-pointer';
                        dotClass += key === selectedDate ? 'bg-white' : 'bg-emerald-400';
                        title = 'Disponible';
                    } else if (state === 'low') {
                        btnClass += key === selectedDate ?
                            'bg-primary text-white shadow-md scale-105' :
                            'hover:bg-orange-50 hover:text-orange-700 text-on-surface cursor-pointer';
                        dotClass += key === selectedDate ? 'bg-white' : 'bg-orange-400';
                        title = 'Pocos slots';
                    } else if (state === 'full') {
                        btnClass += 'text-on-surface-variant/40 cursor-not-allowed';
                        dotClass += 'bg-on-surface-variant/20';
                        disabled = true;
                        title = 'Sin disponibilidad';
                    } else {
                        // No hay datos → tratar como sin disponibilidad
                        btnClass += 'text-on-surface-variant/40 cursor-not-allowed';
                        dotClass = '';
                        disabled = true;
                    }

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.disabled = disabled;
                    btn.title = title;
                    btn.className = btnClass;
                    btn.innerHTML = `<span>${day}</span>${dotClass ? `<span class="${dotClass}"></span>` : ''}`;

                    if (!disabled) {
                        btn.addEventListener('click', () => selectDay(key));
                    }

                    gridEl.appendChild(btn);
                }
            }

            // ── Render ambos meses ────────────────────────────────────────────
            function renderCalendar() {
                const leftGrid = document.getElementById('dp-grid-left');
                const rightGrid = document.getElementById('dp-grid-right');
                const leftLabel = document.getElementById('dp-month-left');
                const rightLabel = document.getElementById('dp-month-right');
                const prevBtn = document.getElementById('dp-prev');

                if (!leftGrid) return;

                const nextMonth = currentMonth === 11 ? 0 : currentMonth + 1;
                const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;

                if (leftLabel) leftLabel.textContent = `${MONTHS_ES[currentMonth]} ${currentYear}`;
                if (rightLabel) rightLabel.textContent = `${MONTHS_ES[nextMonth]} ${nextYear}`;

                // Deshabilitar prev si el mes actual es el presente o pasado
                const nowMonth = new Date().getMonth();
                const nowYear = new Date().getFullYear();
                if (prevBtn) {
                    prevBtn.disabled = (currentYear === nowYear && currentMonth <= nowMonth);
                }

                renderMonth(leftGrid, currentYear, currentMonth);
                renderMonth(rightGrid, nextYear, nextMonth);
            }

            // ── Seleccionar día → cargar slots de hora ────────────────────────
            async function selectDay(dateKey) {
                // Si cambia el día, resetear slot y profesionales en Livewire
                if (selectedDate && selectedDate !== dateKey) {
                    $wire.set('fecha', null);
                    $wire.set('hora', null);
                    $wire.set('horaFin', null);
                    $wire.set('selectedProfesionales', {});
                    $wire.set('profesionalesPorServicio', {});
                    // Limpiar pills del día anterior visualmente
                    document.querySelectorAll('.slot-pill').forEach(p => {
                        p.classList.remove('border-primary', 'bg-primary', 'text-white', 'shadow-md');
                        p.classList.add('border-outline-variant/30', 'bg-surface-container', 'text-on-surface');
                    });
                }

                selectedDate = dateKey;
                renderCalendar(); // resalta el día seleccionado

                const wrapper = document.getElementById('dp-slots-wrapper');
                const title = document.getElementById('dp-slots-title');
                const grid = document.getElementById('dp-slots-grid');
                const empty = document.getElementById('dp-slots-empty');
                const loading = document.getElementById('dp-slots-loading');

                if (!wrapper || !grid) return;

                wrapper.classList.remove('hidden');
                grid.innerHTML = '';
                empty.classList.add('hidden');
                loading.classList.remove('hidden');

                // Formatear título del día
                const d = new Date(dateKey + 'T12:00:00');
                const DAYS_ES = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
                title.textContent = `Horarios para el ${DAYS_ES[d.getDay()]} ${d.getDate()} de ${MONTHS_ES[d.getMonth()]}`;

                // Consultar endpoint para el día específico
                const start = new Date(dateKey + 'T00:00:00');
                const end = new Date(dateKey + 'T23:59:59');
                const params = new URLSearchParams({
                    company_id: companyId,
                    start: start.toISOString(),
                    end: end.toISOString(),
                });
                serviceIds.forEach(id => params.append('services[]', id));

                try {
                    let disponibles;

                    if (slotsCache[dateKey]) {
                        // Reusar datos ya cargados — sin request al servidor
                        disponibles = slotsCache[dateKey];
                        loading.classList.add('hidden');
                    } else {
                        // Solo si no está en cache (caso raro: día fuera del rango pre-cargado)
                        const res = await fetch(`/booking/citas-ocupadas?${params}`);
                        const data = await res.json();
                        disponibles = (data.disponibles || []);
                        loading.classList.add('hidden');
                    }

                    const ahora = new Date();
                    const slotsDisponibles = disponibles
                        .filter(s => {
                            if (s.fecha !== dateKey || s.disponibles === 0) return false;
                            return new Date(`${s.fecha}T${s.inicio}`) > ahora;
                        })
                        .sort((a, b) => a.inicio.localeCompare(b.inicio));

                    if (slotsDisponibles.length === 0) {
                        empty.classList.remove('hidden');
                        return;
                    }

                    slotsDisponibles.forEach(slot => {
                        const pill = document.createElement('button');
                        pill.type = 'button';
                        pill.className = 'slot-pill px-4 py-2 rounded-xl text-sm font-semibold border-2 transition-all duration-150 border-outline-variant/30 bg-surface-container hover:border-primary/50 hover:bg-primary/5 hover:text-primary text-on-surface';
                        pill.textContent = slot.inicio;
                        pill.dataset.hora = slot.inicio;

                        if (slot.disponibles === 1 && serviceIds.length === 1) {
                            pill.insertAdjacentHTML('beforeend', '<span class="ml-1.5 text-[10px] text-orange-500 font-bold">·último</span>');
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

            // ── Seleccionar slot de hora → notificar a Livewire ───────────────
            function selectSlot(fecha, hora, pillEl) {
                // Resaltar pill seleccionada
                document.querySelectorAll('.slot-pill').forEach(p => {
                    p.classList.remove('border-primary', 'bg-primary', 'text-white', 'shadow-md');
                    p.classList.add('border-outline-variant/30', 'bg-surface-container', 'text-on-surface');
                });
                pillEl.classList.remove('border-outline-variant/30', 'bg-surface-container', 'text-on-surface');
                pillEl.classList.add('border-primary', 'bg-primary', 'text-white', 'shadow-md');

                $wire.call('seleccionarSlot', fecha, hora);

                // Scroll suave al paso 4
                setTimeout(() => {
                    const paso4 = document.querySelector('[data-paso="4"]');
                    if (paso4) paso4.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 300);
            }

            // ── Navegación de meses ───────────────────────────────────────────
            function setupNav() {
                document.getElementById('dp-prev')?.addEventListener('click', () => {
                    const now = new Date();
                    if (currentYear === now.getFullYear() && currentMonth <= now.getMonth()) return;
                    if (currentMonth === 0) {
                        currentMonth = 11;
                        currentYear--;
                    } else currentMonth--;
                    renderCalendar();
                });

                document.getElementById('dp-next')?.addEventListener('click', () => {
                    if (currentMonth === 11) {
                        currentMonth = 0;
                        currentYear++;
                    } else currentMonth++;
                    renderCalendar();
                });
            }

            // ── Init ──────────────────────────────────────────────────────────
            function init() {
                if (!readParams()) return;
                const now = new Date();
                currentYear = now.getFullYear();
                currentMonth = now.getMonth();
                selectedDate = null;
                availability = {};
                slotsCache = {};

                // Ocultar slots panel
                const wrapper = document.getElementById('dp-slots-wrapper');
                if (wrapper) wrapper.classList.add('hidden');

                setupNav();
                renderCalendar();
                loadAvailability();
            }

            // ── Escuchar eventos de Livewire ──────────────────────────────────
            document.addEventListener('livewire:initialized', () => {
                if (document.getElementById('dp-data')) init();
            });

            $wire.on('empresa-cargada', () => {
                setTimeout(init, 80);
            });

            $wire.on('servicios-actualizados', () => {
                setTimeout(init, 80);
            });

            $wire.on('reset-calendario', () => {
                selectedDate = null;
                availability = {};
                const wrapper = document.getElementById('dp-slots-wrapper');
                if (wrapper) wrapper.classList.add('hidden');
            });

        })();
    </script>
    @endscript
</div>