<x-app-layout>
    <main class="flex-1 bg-surface px-8 py-8">

        <div class="max-w-7xl mx-auto flex flex-col gap-8">

            {{-- HEADER --}}
            <header class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-on-surface">
                        Buenos días, {{ auth()->user()->name }}
                    </h1>
                    <p class="text-sm text-on-surface-variant mt-1">
                        {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                    </p>
                </div>

                {{-- TOGGLE PERIODO --}}
                <div class="flex gap-1 bg-surface-container rounded-full p-1 border border-outline-variant/40">
                    <button data-period="hoy" class="period-btn btn-toggle">Hoy</button>
                    <button data-period="semana" class="period-btn btn-toggle">Semana</button>
                    <button data-period="mes" class="period-btn btn-toggle">Mes</button>
                </div>
            </header>

            {{-- KPIs --}}
            <section id="kpi-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3"></section>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT --}}
                <div class="lg:col-span-2 flex flex-col gap-6">

                    {{-- CHART --}}
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-semibold text-primary">Ocupación</h2>

                            {{-- TOGGLE CHART --}}
                            <div
                                class="flex gap-1 bg-surface-container rounded-full p-1 border border-outline-variant/40">
                                <button data-chart="barras" class="chart-btn btn-toggle">Barras</button>
                                <button data-chart="lineas" class="chart-btn btn-toggle">Líneas</button>
                            </div>
                        </div>

                        <div class="h-64">
                            <canvas id="mainChart"></canvas>
                        </div>
                    </div>

                    {{-- APPOINTMENTS --}}
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm">
                        <h2 class="font-semibold text-primary mb-4">Próximas citas</h2>
                        <div id="appts-list" class="flex flex-col gap-3"></div>
                    </div>

                </div>

                {{-- RIGHT --}}
                <div class="flex flex-col gap-6">

                    {{-- SERVICES --}}
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm">
                        <h2 class="font-semibold text-primary mb-4">
                            Servicios más solicitados
                        </h2>
                        <div id="services-list" class="flex flex-col gap-3"></div>
                    </div>

                    {{-- ATTENDANCE --}}
                    <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">
                        <p class="text-xs uppercase tracking-widest text-primary">
                            Tasa de asistencia
                        </p>

                        <div id="asistencia-value" class="text-5xl font-extrabold text-primary"></div>

                        <p id="asistencia-sub" class="text-sm text-on-surface-variant"></p>

                        <div class="h-1.5 bg-black/10 rounded-full overflow-hidden">
                            <div id="asistencia-bar" class="h-full bg-primary transition-all duration-500"></div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </main>

    {{-- ESTILOS --}}
    <style>
        .btn-toggle {
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            color: #524438;
            transition: all .2s ease;
        }

        .btn-toggle:hover {
            background: #ebe8e2;
        }

        .btn-active {
            background: #663a00;
            color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .fade-in {
            animation: fade .25s ease;
        }

        @keyframes fade {
            from {
                opacity: 0;
                transform: translateY(4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    {{-- JS --}}
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

        <script>
            const DATA = @json($kpis);
            const APPTS = @json($appointments);
            const SERVICES = @json($services);

            // PERSISTENCIA
            let currentPeriod = localStorage.getItem('dashboard_period') || 'hoy';
            let currentChart = localStorage.getItem('dashboard_chart') || 'barras';

            let chartInstance = null;

            function setActive(selector, value, attr) {
                document.querySelectorAll(selector).forEach(btn => {
                    btn.classList.remove('btn-active');
                    if (btn.dataset[attr] === value) {
                        btn.classList.add('btn-active');
                    }
                });
            }

            function renderKPIs(period) {
                document.getElementById('kpi-grid').innerHTML =
                    DATA[period].kpis.map(k => `
                        <div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/20 fade-in">
                            <span class="text-xs text-on-surface-variant">${k.label}</span>
                            <span class="text-3xl font-bold text-on-surface mt-1">${k.value}</span>
                        </div>
                    `).join('');
            }

            function renderChart(period, type) {

                if (chartInstance) chartInstance.destroy();

                const isLine = type === 'lineas';

                const colors = {
                    primary: '#663a00',
                    soft: '#ffb870',
                    success: '#046289',
                    error: '#ba1a1a'
                };

                let data = JSON.parse(JSON.stringify(DATA[period][type]));

                data.datasets = data.datasets.map((ds, i) => {

                    if (!isLine) {
                        return {
                            ...ds,
                            backgroundColor: colors.soft,
                            borderRadius: 8
                        };
                    }

                    return {
                        ...ds,
                        borderColor: i === 0 ? colors.success : colors.error,
                        tension: 0.4,
                        pointRadius: 3
                    };
                });

                chartInstance = new Chart(document.getElementById('mainChart'), {
                    type: isLine ? 'line' : 'bar',
                    data,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            function renderServices(period) {
                if (!SERVICES || !SERVICES[period]) return;

                document.getElementById('services-list').innerHTML =
                    SERVICES[period].map(([name, count]) => `
                        <div class="flex justify-between fade-in">
                            <span class="text-on-surface">${name}</span>
                            <span class="text-on-surface-variant">${count}</span>
                        </div>
                    `).join('');
            }

            function renderAppts() {
                document.getElementById('appts-list').innerHTML =
                    APPTS.map(a => `
                        <div class="p-4 rounded-xl bg-surface-container-low fade-in">
                            <b class="text-primary">${a.time}</b>
                            <span class="ml-2 text-on-surface">${a.name}</span>
                        </div>
                    `).join('');
            }

            function renderAsistencia(period) {
                const a = DATA[period].asistencia;

                document.getElementById('asistencia-value').textContent = a.pct + '%';
                document.getElementById('asistencia-sub').textContent = a.text;
                document.getElementById('asistencia-bar').style.width = a.pct + '%';
            }

            function updateDashboard() {
                setActive('.period-btn', currentPeriod, 'period');
                setActive('.chart-btn', currentChart, 'chart');

                renderKPIs(currentPeriod);
                renderChart(currentPeriod, currentChart);
                renderAppts();
                renderAsistencia(currentPeriod);

                if (SERVICES) renderServices(currentPeriod);
            }

            // EVENTS
            document.querySelectorAll('.period-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    currentPeriod = btn.dataset.period;

                    localStorage.setItem('dashboard_period', currentPeriod);

                    updateDashboard();
                });
            });

            document.querySelectorAll('.chart-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    currentChart = btn.dataset.chart;

                    localStorage.setItem('dashboard_chart', currentChart);

                    renderChart(currentPeriod, currentChart);
                    setActive('.chart-btn', currentChart, 'chart');
                });
            });

            updateDashboard();
        </script>
    @endpush
</x-app-layout>
