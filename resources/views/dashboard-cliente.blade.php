<x-app-layout>
    {{-- Material Symbols Rounded --}}
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <main class="flex-1 bg-surface px-8 py-8">
        <div class="max-w-5xl mx-auto flex flex-col gap-8">

            {{-- HEADER --}}
            <header class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-rounded text-primary ms-outline"
                        style="font-size: 2rem;">waving_hand</span>
                    <div>
                        <h1 class="text-3xl font-bold text-on-surface">
                            Hola, {{ auth()->user()->name }}
                        </h1>
                        <p class="text-sm text-on-surface-variant mt-1">
                            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </p>
                    </div>
                </div>
            </header>

            @if (!$customer)
                {{-- SIN PERFIL --}}
                <div
                    class="bg-surface-container-lowest rounded-xl p-10 border border-outline-variant/20 text-center flex flex-col items-center gap-3">
                    <span class="material-symbols-rounded text-on-surface-variant ms-outline"
                        style="font-size: 2.5rem;">person_off</span>
                    <p class="text-on-surface-variant text-sm">
                        Aún no tienes un perfil de cliente vinculado a tu cuenta.
                    </p>
                </div>
            @else
                {{-- PRÓXIMA CITA BANNER --}}
                @if ($nextAppointment)
                    <div
                        class="bg-primary text-white rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-md">
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-rounded opacity-80"
                                style="font-size: 2rem; margin-top: 2px;">event</span>
                            <div>
                                <p class="text-xs uppercase tracking-widest opacity-70 mb-1">Próxima cita</p>
                                <p class="text-2xl font-bold">
                                    {{ $nextAppointment->start_time->isoFormat('ddd D [de] MMMM') }}
                                    &mdash;
                                    {{ $nextAppointment->start_time->format('H:i') }}
                                </p>
                                <p class="text-sm opacity-80 mt-1 flex items-center gap-1">
                                    <span class="material-symbols-rounded ms-outline"
                                        style="font-size: 1rem;">auto_fix_normal</span>
                                    {{ $nextAppointment->services->pluck('name')->join(', ') }}
                                </p>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <span
                                class="inline-flex items-center gap-1.5 bg-white/20 text-white text-xs font-semibold px-4 py-2 rounded-full">
                                <span class="material-symbols-rounded" style="font-size: 0.9rem;">schedule</span>
                                {{ $nextAppointment->start_time->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @else
                    <div
                        class="bg-surface-container rounded-2xl p-6 border border-outline-variant/20 text-center flex items-center justify-center gap-2">
                        <span class="material-symbols-rounded text-on-surface-variant ms-outline"
                            style="font-size: 1.25rem;">event_busy</span>
                        <p class="text-on-surface-variant text-sm">No tienes citas próximas agendadas.</p>
                    </div>
                @endif

                {{-- KPI CARDS --}}
                <section class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $kpiCards = [
                            [
                                'label' => 'Total citas',
                                'value' => $stats['total'],
                                'icon' => 'calendar_month',
                                'color' => 'text-primary',
                            ],
                            [
                                'label' => 'Completadas',
                                'value' => $stats['completadas'],
                                'icon' => 'task_alt',
                                'color' => 'text-emerald-600',
                            ],
                            [
                                'label' => 'Canceladas',
                                'value' => $stats['canceladas'],
                                'icon' => 'event_busy',
                                'color' => 'text-red-500',
                            ],
                            [
                                'label' => 'Activas',
                                'value' => $stats['activas'],
                                'icon' => 'pending_actions',
                                'color' => 'text-amber-500',
                            ],
                        ];
                    @endphp

                    @foreach ($kpiCards as $card)
                        <div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/20">
                            <span class="material-symbols-rounded {{ $card['color'] }}"
                                style="font-size: 1.5rem;">{{ $card['icon'] }}</span>
                            <p class="text-xs text-on-surface-variant mt-2">{{ $card['label'] }}</p>
                            <p class="text-3xl font-bold text-on-surface mt-1">{{ $card['value'] }}</p>
                        </div>
                    @endforeach
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- LEFT: Gráfico + Citas próximas --}}
                    <div class="lg:col-span-2 flex flex-col gap-6">

                        {{-- GRÁFICO ACTIVIDAD 30 DÍAS --}}
                        <div
                            class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm">
                            <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
                                <span class="material-symbols-rounded ms-outline"
                                    style="font-size: 1.1rem;">bar_chart</span>
                                Actividad — últimos 30 días
                            </h2>
                            <div class="h-48">
                                <canvas id="activityChart"></canvas>
                            </div>
                        </div>

                        {{-- PRÓXIMAS CITAS --}}
                        @if ($upcoming->count())
                            <div
                                class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm">
                                <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
                                    <span class="material-symbols-rounded ms-outline"
                                        style="font-size: 1.1rem;">upcoming</span>
                                    Citas próximas
                                </h2>
                                <div class="flex flex-col gap-3">
                                    @foreach ($upcoming as $a)
                                        <div
                                            class="flex items-center justify-between p-4 rounded-xl bg-surface-container-low">
                                            <div class="flex items-start gap-3">
                                                <span class="material-symbols-rounded text-primary ms-outline mt-0.5"
                                                    style="font-size: 1.1rem;">calendar_today</span>
                                                <div>
                                                    <p class="text-sm font-semibold text-on-surface">
                                                        {{ $a['date'] }} · {{ $a['time'] }}
                                                    </p>
                                                    <p class="text-xs text-on-surface-variant mt-0.5">
                                                        {{ $a['services'] }}
                                                        @if ($a['staff'] !== 'Sin asignar')
                                                            &mdash; {{ $a['staff'] }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <span @class([
                                                'inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full',
                                                'bg-green-100 text-green-700' => $a['status'] === 'confirmed',
                                                'bg-yellow-100 text-yellow-700' => $a['status'] === 'pending',
                                            ])>
                                                <span class="material-symbols-rounded" style="font-size: 0.85rem;">
                                                    {{ $a['status'] === 'confirmed' ? 'check_circle' : 'hourglass_empty' }}
                                                </span>
                                                {{ $a['label'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- HISTORIAL RECIENTE --}}
                        @if ($history->count())
                            <div
                                class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm">
                                <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
                                    <span class="material-symbols-rounded ms-outline"
                                        style="font-size: 1.1rem;">history</span>
                                    Historial reciente
                                </h2>
                                <div class="flex flex-col gap-3">
                                    @foreach ($history as $a)
                                        <div
                                            class="flex items-center justify-between p-4 rounded-xl bg-surface-container-low">
                                            <div class="flex items-start gap-3">
                                                <span
                                                    class="material-symbols-rounded text-on-surface-variant ms-outline mt-0.5"
                                                    style="font-size: 1.1rem;">event_note</span>
                                                <div>
                                                    <p class="text-sm font-semibold text-on-surface">
                                                        {{ $a['date'] }} · {{ $a['time'] }}
                                                    </p>
                                                    <p class="text-xs text-on-surface-variant mt-0.5">
                                                        {{ $a['services'] }}
                                                    </p>
                                                </div>
                                            </div>
                                            <span @class([
                                                'inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full',
                                                'bg-blue-100 text-blue-700' => $a['status'] === 'completed',
                                                'bg-red-100 text-red-600' => $a['status'] === 'cancelled',
                                            ])>
                                                <span class="material-symbols-rounded" style="font-size: 0.85rem;">
                                                    {{ $a['status'] === 'completed' ? 'verified' : 'cancel' }}
                                                </span>
                                                {{ $a['label'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- RIGHT: Asistencia + Servicios favoritos + Perfil --}}
                    <div class="flex flex-col gap-6">

                        {{-- TASA DE ASISTENCIA --}}
                        <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">
                            <p class="text-xs uppercase tracking-widest text-primary flex items-center gap-1.5">
                                <span class="material-symbols-rounded" style="font-size: 1rem;">monitoring</span>
                                Tasa de asistencia
                            </p>
                            <div class="text-5xl font-extrabold text-primary">
                                {{ $stats['attendedPct'] }}%
                            </div>
                            <p class="text-sm text-on-surface-variant">
                                {{ $stats['completadas'] }} de {{ $stats['total'] }} citas completadas
                            </p>
                            <div class="h-1.5 bg-black/10 rounded-full overflow-hidden">
                                <div class="h-full bg-primary transition-all duration-500"
                                    style="width: {{ $stats['attendedPct'] }}%">
                                </div>
                            </div>
                        </div>

                        {{-- SERVICIOS FAVORITOS --}}
                        @if ($topServices->count())
                            <div
                                class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm">
                                <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
                                    <span class="material-symbols-rounded ms-outline"
                                        style="font-size: 1.1rem;">star</span>
                                    Tus servicios favoritos
                                </h2>
                                <div class="flex flex-col gap-3">
                                    @foreach ($topServices as $s)
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="material-symbols-rounded text-on-surface-variant ms-outline"
                                                    style="font-size: 1rem;">auto_fix_normal</span>
                                                <span class="text-sm text-on-surface">{{ $s->name }}</span>
                                            </div>
                                            <span
                                                class="text-xs font-semibold bg-primary/10 text-primary px-2 py-0.5 rounded-full">
                                                {{ $s->count }}x
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- DATOS DEL PERFIL --}}
                        <div
                            class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-3">
                            <h2 class="font-semibold text-primary flex items-center gap-2">
                                <span class="material-symbols-rounded ms-outline"
                                    style="font-size: 1.1rem;">manage_accounts</span>
                                Mi perfil
                            </h2>
                            <div class="space-y-2.5 text-sm">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="flex items-center gap-1.5 text-on-surface-variant shrink-0">
                                        <span class="material-symbols-rounded ms-outline"
                                            style="font-size: 1rem;">person</span>
                                        Nombre
                                    </span>
                                    <span class="text-on-surface font-medium text-right">{{ $customer->name }}</span>
                                </div>
                                @if ($customer->phone)
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="flex items-center gap-1.5 text-on-surface-variant shrink-0">
                                            <span class="material-symbols-rounded ms-outline"
                                                style="font-size: 1rem;">phone</span>
                                            Teléfono
                                        </span>
                                        <span class="text-on-surface font-medium">{{ $customer->phone }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between gap-2">
                                    <span class="flex items-center gap-1.5 text-on-surface-variant shrink-0">
                                        <span class="material-symbols-rounded ms-outline"
                                            style="font-size: 1rem;">mail</span>
                                        Email
                                    </span>
                                    <span
                                        class="text-on-surface font-medium truncate max-w-[140px]">{{ $customer->email }}</span>
                                </div>
                                @if ($customer->created_at)
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="flex items-center gap-1.5 text-on-surface-variant shrink-0">
                                            <span class="material-symbols-rounded ms-outline"
                                                style="font-size: 1rem;">cake</span>
                                            Cliente desde
                                        </span>
                                        <span class="text-on-surface font-medium">
                                            {{ $customer->created_at->isoFormat('MMM YYYY') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            @endif
        </div>
    </main>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
        <script>
            @if ($customer)
                const CHART_LABELS = @json($stats['chart']['labels']);
                const CHART_DATA = @json($stats['chart']['data']);

                new Chart(document.getElementById('activityChart'), {
                    type: 'bar',
                    data: {
                        labels: CHART_LABELS,
                        datasets: [{
                            label: 'Citas',
                            data: CHART_DATA,
                            backgroundColor: '#663a00',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxTicksLimit: 6,
                                    font: {
                                        size: 10
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        }
                    }
                });
            @endif
        </script>
    @endpush
</x-app-layout>
