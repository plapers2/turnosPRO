<div>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    {{-- LOADING BAR --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    <main class="flex-1 bg-surface px-8 py-8">
        <div class="max-w-5xl mx-auto flex flex-col gap-8">

            {{-- HEADER --}}
            <header class="flex flex-col gap-1">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-rounded text-primary ms-outline"
                        style="font-size:2rem;">waving_hand</span>
                    <div>
                        <h1 class="text-3xl font-bold text-on-surface">
                            Hola, {{ auth()->user()->name }}
                        </h1>
                        <p class="text-sm text-on-surface-variant mt-1 flex items-center gap-1.5">
                            <span class="material-symbols-rounded ms-outline"
                                style="font-size:1rem;">calendar_today</span>
                            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </p>
                    </div>
                </div>
            </header>

            @if (!$customer)
                <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/20 text-center">
                    <span class="material-symbols-rounded text-on-surface-variant"
                        style="font-size:3rem;">person_off</span>
                    <p class="text-on-surface-variant mt-2">No tienes un perfil de cliente asociado todavía.</p>
                </div>
            @else
                {{-- PRÓXIMA CITA DESTACADA --}}
                @if ($nextAppointment)
                    <div
                        class="bg-primary-container/20 rounded-2xl p-6 border border-primary/20 flex items-center gap-4">
                        <span class="material-symbols-rounded text-primary"
                            style="font-size:2rem;">event_upcoming</span>
                        <div>
                            <p class="text-xs uppercase tracking-widest text-primary mb-1">Próxima cita</p>
                            <p class="text-lg font-bold text-on-surface">
                                {{ $nextAppointment->start_time->isoFormat('dddd D [de] MMMM') }}
                                <span
                                    class="text-primary">&nbsp;{{ $nextAppointment->start_time->format('H:i') }}</span>
                            </p>
                            <p class="text-sm text-on-surface-variant">
                                {{ $nextAppointment->services->pluck('name')->join(', ') }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- KPIs --}}
                <section class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $statsMeta = [
                            [
                                'label' => 'Total',
                                'value' => $stats['total'],
                                'icon' => 'calendar_month',
                                'accent' => 'bg-primary',
                                'wrap' => 'bg-primary-container text-on-primary-container',
                            ],
                            [
                                'label' => 'Completadas',
                                'value' => $stats['completadas'],
                                'icon' => 'check_circle',
                                'accent' => 'bg-[#0F6E56]',
                                'wrap' => 'bg-[#DDF4E8] text-[#0F6E56]',
                            ],
                            [
                                'label' => 'Canceladas',
                                'value' => $stats['canceladas'],
                                'icon' => 'cancel',
                                'accent' => 'bg-error',
                                'wrap' => 'bg-error-container/60 text-on-error-container',
                            ],
                            [
                                'label' => 'Activas',
                                'value' => $stats['activas'],
                                'icon' => 'schedule',
                                'accent' => 'bg-blue-600',
                                'wrap' => 'bg-blue-200 text-blue-700',
                            ],
                        ];
                    @endphp
                    @foreach ($statsMeta as $s)
                        <div
                            class="relative overflow-hidden flex items-center gap-3 rounded-xl shadow-sm bg-surface-container-lowest px-4 py-3">
                            <div class="absolute left-0 top-2 bottom-2 w-[3px] rounded-r-full {{ $s['accent'] }}">
                            </div>
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] {{ $s['wrap'] }}">
                                <span class="material-symbols-outlined text-[17px]">{{ $s['icon'] }}</span>
                            </div>
                            <div class="flex min-w-0 flex-col gap-0.5">
                                <span
                                    class="text-[17px] font-semibold leading-none text-on-surface">{{ $s['value'] }}</span>
                                <span class="text-[11px] font-normal text-on-surface-variant">{{ $s['label'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- LEFT: Gráfico actividad + próximas --}}
                    <div class="lg:col-span-2 flex flex-col gap-6">

                        {{-- Gráfico 30 días --}}
                        <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/20 shadow-sm"
                            x-data="{
                                init() {
                                    new Chart(this.$refs.canvas.getContext('2d'), {
                                        type: 'bar',
                                        data: {
                                            labels: @js($stats['chart']['labels']),
                                            datasets: [{
                                                label: 'Citas',
                                                data: @js($stats['chart']['data']),
                                                backgroundColor: '#663a00',
                                                borderRadius: 6,
                                            }],
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { display: false } },
                                            scales: {
                                                x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 10 } } },
                                                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } },
                                            },
                                        },
                                    });
                                }
                            }">
                            <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
                                <span class="material-symbols-rounded ms-outline"
                                    style="font-size:1.1rem;">bar_chart</span>
                                Actividad últimos 30 días
                            </h2>
                            <div class="h-48">
                                <canvas x-ref="canvas"></canvas>
                            </div>
                        </div>

                        {{-- Próximas citas --}}
                        <div
                            class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/20 shadow-sm">
                            <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
                                <span class="material-symbols-rounded ms-outline"
                                    style="font-size:1.1rem;">upcoming</span>
                                Próximas citas
                            </h2>
                            @forelse($upcoming as $a)
                                @php
                                    $cfg =
                                        $a['status'] === 'confirmed'
                                            ? [
                                                'bg' => '#f0fdf4',
                                                'color' => '#16a34a',
                                                'icon' => 'check_circle',
                                                'label' => 'Confirmada',
                                            ]
                                            : [
                                                'bg' => '#fffbeb',
                                                'color' => '#d97706',
                                                'icon' => 'hourglass_empty',
                                                'label' => 'Pendiente',
                                            ];
                                @endphp
                                <div
                                    class="flex items-center justify-between p-4 rounded-xl bg-surface-container-low mb-3">
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-rounded text-primary ms-outline mt-0.5"
                                            style="font-size:1.1rem">schedule</span>
                                        <div>
                                            <p class="text-sm font-semibold text-on-surface">
                                                <span class="text-primary">{{ $a['date'] }}
                                                    {{ $a['time'] }}</span>
                                            </p>
                                            <p class="text-xs text-on-surface-variant mt-0.5">{{ $a['services'] }}</p>
                                        </div>
                                    </div>
                                    <span class="status-badge"
                                        style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }}">
                                        <span class="material-symbols-rounded"
                                            style="font-size:.85rem">{{ $cfg['icon'] }}</span>
                                        {{ $cfg['label'] }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-sm text-on-surface-variant flex items-center gap-2">
                                    <span class="material-symbols-rounded ms-outline"
                                        style="font-size:1.1rem">event_busy</span>
                                    No hay citas próximas.
                                </p>
                            @endforelse
                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="flex flex-col gap-6">

                        {{-- Tasa asistencia --}}
                        <div class="bg-primary-container/10 rounded-2xl p-6 border border-primary/10 space-y-3">
                            <p class="text-xs uppercase tracking-widest text-primary flex items-center gap-1.5">
                                <span class="material-symbols-rounded" style="font-size:1rem;">monitoring</span>
                                Tasa de asistencia
                            </p>
                            <div class="text-5xl font-extrabold text-primary">{{ $stats['attendedPct'] }}%</div>
                            <p class="text-sm text-on-surface-variant">{{ $stats['completadas'] }} de
                                {{ $stats['total'] }} citas completadas</p>
                            <div class="h-1.5 bg-black/10 rounded-full overflow-hidden">
                                <div class="h-full bg-primary transition-all duration-500"
                                    style="width: {{ $stats['attendedPct'] }}%"></div>
                            </div>
                        </div>

                        {{-- Top servicios --}}
                        <div
                            class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/20 shadow-sm">
                            <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
                                <span class="material-symbols-rounded ms-outline" style="font-size:1.1rem;">star</span>
                                Mis servicios frecuentes
                            </h2>
                            @forelse($topServices as $s)
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-rounded text-on-surface-variant ms-outline"
                                            style="font-size:1rem">auto_fix_normal</span>
                                        <span class="text-sm text-on-surface">{{ $s->name }}</span>
                                    </div>
                                    <span
                                        class="text-xs font-semibold bg-primary/10 text-primary px-2 py-0.5 rounded-full">{{ $s->count }}x</span>
                                </div>
                            @empty
                                <p class="text-sm text-on-surface-variant">Sin historial de servicios.</p>
                            @endforelse
                        </div>

                        {{-- Historial --}}
                        <div
                            class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/20 shadow-sm">
                            <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
                                <span class="material-symbols-rounded ms-outline"
                                    style="font-size:1.1rem;">history</span>
                                Historial reciente
                            </h2>
                            @forelse($history as $a)
                                @php
                                    $cfg =
                                        $a['status'] === 'completed'
                                            ? [
                                                'bg' => '#eff6ff',
                                                'color' => '#2563eb',
                                                'icon' => 'verified',
                                                'label' => 'Completada',
                                            ]
                                            : [
                                                'bg' => '#fff1f2',
                                                'color' => '#dc2626',
                                                'icon' => 'cancel',
                                                'label' => 'Cancelada',
                                            ];
                                @endphp
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-xs text-on-surface-variant">{{ $a['date'] }}
                                            {{ $a['time'] }}</p>
                                        <p class="text-sm text-on-surface">{{ $a['services'] }}</p>
                                    </div>
                                    <span class="status-badge"
                                        style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }}">
                                        <span class="material-symbols-rounded"
                                            style="font-size:.85rem">{{ $cfg['icon'] }}</span>
                                        {{ $cfg['label'] }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-sm text-on-surface-variant">Sin historial.</p>
                            @endforelse
                        </div>

                    </div>
                </div>

            @endif
        </div>
    </main>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
@endpush
