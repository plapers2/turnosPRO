<div>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    {{-- LOADING BAR --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    <main class="flex-1 bg-surface px-8 py-8">
        <div class="max-w-7xl mx-auto flex flex-col gap-8">

            {{-- HEADER --}}
            <header class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-rounded text-primary ms-outline"
                        style="font-size:2rem;">waving_hand</span>
                    <div>
                        <h1 class="text-3xl font-bold text-on-surface">
                            Hola, {{ auth()->user()->name }}
                        </h1>
                        <p class="text-sm text-on-surface-variant mt-1 flex items-center gap-1.5">
                            <span class="material-symbols-rounded ms-outline text-on-surface-variant"
                                style="font-size:1rem;">calendar_today</span>
                            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </p>
                    </div>
                </div>

                {{-- TOGGLE PERÍODO --}}
                <div class="flex gap-1 bg-surface-container rounded-full p-1 border border-outline-variant/40">
                    <button wire:click.debounce.300ms="setPeriod('hoy')"
                        class="period-btn {{ $period === 'hoy' ? 'btn-active' : '' }}">
                        <span class="material-symbols-rounded ms-outline" style="font-size:.95rem;">today</span>
                        Hoy
                    </button>
                    <button wire:click.debounce.300ms="setPeriod('semana')"
                        class="period-btn {{ $period === 'semana' ? 'btn-active' : '' }}">
                        <span class="material-symbols-rounded ms-outline" style="font-size:.95rem;">date_range</span>
                        Semana
                    </button>
                    <button wire:click.debounce.300ms="setPeriod('mes')"
                        class="period-btn {{ $period === 'mes' ? 'btn-active' : '' }}">
                        <span class="material-symbols-rounded ms-outline"
                            style="font-size:.95rem;">calendar_month</span>
                        Mes
                    </button>
                </div>
            </header>

            {{-- KPIs con skeleton al cambiar período --}}
            <div x-data="{ loading: false }" x-on:period-changed.window="loading = true"
                x-on:kpis-updated.window="loading = false">
                <div x-show="loading" class="grid grid-cols-2 sm:grid-cols-4 gap-3 animate-pulse">
                    <div class="h-[72px] rounded-xl bg-surface-container"></div>
                    <div class="h-[72px] rounded-xl bg-surface-container"></div>
                    <div class="h-[72px] rounded-xl bg-surface-container"></div>
                    <div class="h-[72px] rounded-xl bg-surface-container"></div>
                </div>
                <div x-show="!loading">
                    <livewire:dashboard.kpi-cards :period="$period" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT --}}
                <div class="lg:col-span-2 flex flex-col gap-6">

                    {{-- GRÁFICO con skeleton al cambiar período --}}
                    <div x-data="{ loading: false }" x-on:period-changed.window="loading = true"
                        x-on:chart-data-updated.window="loading = false">
                        <div x-show="loading" class="h-[340px] rounded-2xl bg-surface-container animate-pulse"></div>
                        <div x-show="!loading">
                            <livewire:dashboard.ocupacion-chart :period="$period" :chartType="$chartType" />
                        </div>
                    </div>

                    {{-- PRÓXIMAS CITAS — no depende del período --}}
                    <livewire:dashboard.proximas-citas />

                </div>

                {{-- RIGHT --}}
                <div class="flex flex-col gap-6">

                    {{-- TASA DE ASISTENCIA con skeleton --}}
                    <div x-data="{ loading: false }" x-on:period-changed.window="loading = true"
                        x-on:tasa-updated.window="loading = false">
                        <div x-show="loading" class="h-40 rounded-2xl bg-surface-container animate-pulse"></div>
                        <div x-show="!loading">
                            <livewire:dashboard.tasa-asistencia :period="$period" />
                        </div>
                    </div>

                    {{-- SERVICIOS con skeleton --}}
                    <div x-data="{ loading: false }" x-on:period-changed.window="loading = true"
                        x-on:servicios-updated.window="loading = false">
                        <div x-show="loading" class="h-48 rounded-2xl bg-surface-container animate-pulse"></div>
                        <div x-show="!loading">
                            <livewire:dashboard.servicios-solicitados :period="$period" />
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </main>
</div>
