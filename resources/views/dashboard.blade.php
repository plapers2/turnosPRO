<x-app-layout>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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
                    <button data-period="hoy" class="period-btn">
                        <span class="material-symbols-rounded ms-outline" style="font-size:.95rem;">today</span>
                        Hoy
                    </button>
                    <button data-period="semana" class="period-btn">
                        <span class="material-symbols-rounded ms-outline" style="font-size:.95rem;">date_range</span>
                        Semana
                    </button>
                    <button data-period="mes" class="period-btn">
                        <span class="material-symbols-rounded ms-outline"
                            style="font-size:.95rem;">calendar_month</span>
                        Mes
                    </button>
                </div>
            </header>

            {{-- KPIs --}}
            <section id="kpi-grid" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></section>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT --}}
                <div class="lg:col-span-2 flex flex-col gap-6">

                    {{-- GRÁFICO --}}
                    <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/20 shadow-sm">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="font-semibold text-primary flex items-center gap-2">
                                <span class="material-symbols-rounded ms-outline"
                                    style="font-size:1.1rem;">bar_chart</span>
                                Ocupación
                            </h2>
                            <div
                                class="flex gap-1 bg-surface-container rounded-full p-1 border border-outline-variant/40">
                                <button data-chart="barras" class="chart-btn">
                                    <span class="material-symbols-rounded ms-outline"
                                        style="font-size:.9rem;">bar_chart</span>
                                    Barras
                                </button>
                                <button data-chart="lineas" class="chart-btn">
                                    <span class="material-symbols-rounded ms-outline"
                                        style="font-size:.9rem;">show_chart</span>
                                    Líneas
                                </button>
                            </div>
                        </div>
                        <div class="h-64">
                            <canvas id="mainChart"></canvas>
                        </div>
                    </div>

                    {{-- PRÓXIMAS CITAS --}}
                    <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/20 shadow-sm">
                        <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
                            <span class="material-symbols-rounded ms-outline" style="font-size:1.1rem;">upcoming</span>
                            Próximas citas
                        </h2>
                        <div id="appts-list" class="flex flex-col gap-3"></div>
                    </div>

                </div>

                {{-- RIGHT --}}
                <div class="flex flex-col gap-6">

                    {{-- TASA DE ASISTENCIA --}}
                    <div class="bg-primary-container/10 rounded-2xl p-6 border border-primary/10 space-y-3">
                        <p class="text-xs uppercase tracking-widest text-primary flex items-center gap-1.5">
                            <span class="material-symbols-rounded" style="font-size:1rem;">monitoring</span>
                            Tasa de asistencia
                        </p>
                        <div id="asistencia-value" class="text-5xl font-extrabold text-primary"></div>
                        <p id="asistencia-sub" class="text-sm text-on-surface-variant"></p>
                        <div class="h-1.5 bg-black/10 rounded-full overflow-hidden">
                            <div id="asistencia-bar" class="h-full bg-primary transition-all duration-500"></div>
                        </div>
                    </div>

                    {{-- SERVICIOS MÁS SOLICITADOS --}}
                    <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/20 shadow-sm">
                        <h2 class="font-semibold text-primary mb-4 flex items-center gap-2">
                            <span class="material-symbols-rounded ms-outline" style="font-size:1.1rem;">star</span>
                            Servicios más solicitados
                        </h2>
                        <div id="services-list" class="flex flex-col gap-3"></div>
                    </div>

                </div>
            </div>

        </div>
    </main>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

        <script>
            const DATA = @json($kpis);
            const APPTS = @json($appointments);
            const SERVICES = @json($services);

            let currentPeriod = localStorage.getItem('dashboard_period') || 'hoy';
            let currentChart = localStorage.getItem('dashboard_chart') || 'barras';
            let chartInstance = null;

            function setActive(selector, value, attr) {
                document.querySelectorAll(selector).forEach(btn => {
                    btn.classList.toggle('btn-active', btn.dataset[attr] === value);
                });
            }

            function renderKPIs(period) {

                document.getElementById('kpi-grid').innerHTML =
                    DATA[period].kpis.map((k, i) => {

                        const meta = [{
                                icon: 'calendar_month',
                                wrap: 'bg-primary-container text-on-primary-container',
                                accent: 'bg-primary'
                            },
                            {
                                icon: 'check_circle',
                                wrap: 'bg-[#DDF4E8] text-[#0F6E56]',
                                accent: 'bg-[#0F6E56]'
                            },
                            {
                                icon: 'cancel',
                                wrap: 'bg-error-container/60 text-on-error-container',
                                accent: 'bg-error'
                            },
                            {
                                icon: 'schedule',
                                wrap: 'bg-blue-200 text-blue-700',
                                accent: 'bg-blue-600'
                            }
                        ][i];

                        return `
            <div
                class="
                    relative overflow-hidden
                    flex items-center gap-3
                    rounded-xl
                    shadow-sm
                    bg-surface-container-lowest
                    px-4 py-3
                    transition-all duration-200
                    hover:border-outline-variant
                    hover:bg-surface-container-lowest
                "
            >

                <!-- Accent -->
                <div class="
                    absolute left-0 top-2 bottom-2 w-[3px]
                    rounded-r-full
                    ${meta.accent}
                "></div>

                <!-- Icon -->
                <div class="
                    flex h-9 w-9 shrink-0 items-center justify-center
                    rounded-[10px]
                    ${meta.wrap}
                ">
                    <span class="material-symbols-outlined text-[17px]">
                        ${meta.icon}
                    </span>
                </div>

                <!-- Content -->
                <div class="flex min-w-0 flex-col gap-0.5">

                    <span class="
                        text-[17px]
                        font-semibold
                        leading-none
                        text-on-surface
                    ">
                        ${k.value}
                    </span>

                    <span class="
                        text-[11px]
                        font-normal
                        text-on-surface-variant
                    ">
                        ${k.label}
                    </span>

                </div>

            </div>
            `;
                    }).join('');
            }

            function renderChart(period, type) {
                if (chartInstance) chartInstance.destroy();

                const isLine = type === 'lineas';
                let data = JSON.parse(JSON.stringify(DATA[period][type]));

                data.datasets = data.datasets.map((ds, i) => isLine ? {
                    ...ds,
                    borderColor: i === 0 ? '#046289' : '#ba1a1a',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointRadius: 3
                } : {
                    ...ds,
                    backgroundColor: '#663a00',
                    borderRadius: 8
                });

                chartInstance = new Chart(document.getElementById('mainChart'), {
                    type: isLine ? 'line' : 'bar',
                    data,
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
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }

            const STATUS_CFG = {
                confirmed: {
                    label: 'Confirmada',
                    icon: 'check_circle',
                    bg: '#f0fdf4',
                    color: '#16a34a'
                },
                pending: {
                    label: 'Pendiente',
                    icon: 'hourglass_empty',
                    bg: '#fffbeb',
                    color: '#d97706'
                },
                completed: {
                    label: 'Completada',
                    icon: 'verified',
                    bg: '#eff6ff',
                    color: '#2563eb'
                },
                cancelled: {
                    label: 'Cancelada',
                    icon: 'cancel',
                    bg: '#fff1f2',
                    color: '#dc2626'
                },
            };

            function statusBadge(status) {
                const c = STATUS_CFG[status] || {
                    label: status,
                    icon: 'info',
                    bg: '#f5f5f5',
                    color: '#555'
                };
                return `<span class="status-badge" style="background:${c.bg};color:${c.color}">
                            <span class="material-symbols-rounded" style="font-size:.85rem">${c.icon}</span>
                            ${c.label}
                        </span>`;
            }

            function renderAppts() {
                if (!APPTS?.length) {
                    document.getElementById('appts-list').innerHTML =
                        `<p class="text-sm text-on-surface-variant flex items-center gap-2">
                            <span class="material-symbols-rounded ms-outline" style="font-size:1.1rem">event_busy</span>
                            No hay citas próximas.
                        </p>`;
                    return;
                }
                document.getElementById('appts-list').innerHTML = APPTS.map(a => `
                    <div class="flex items-center justify-between p-4 rounded-xl bg-surface-container-low fade-in">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-primary ms-outline mt-0.5" style="font-size:1.1rem">schedule</span>
                            <div>
                                <p class="text-sm font-semibold text-on-surface">
                                    <span class="text-primary">${a.time}</span>
                                    &nbsp;·&nbsp;${a.name}
                                </p>
                                <p class="text-xs text-on-surface-variant mt-0.5">
                                    ${a.service}${a.staff !== 'Sin asignar' ? ' &mdash; ' + a.staff : ''}
                                </p>
                            </div>
                        </div>
                        ${statusBadge(a.status)}
                    </div>`).join('');
            }

            function renderServices(period) {
                if (!SERVICES?.[period]) return;
                document.getElementById('services-list').innerHTML = SERVICES[period].map(([name, count]) => `
                    <div class="flex items-center justify-between fade-in">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-rounded text-on-surface-variant ms-outline" style="font-size:1rem">auto_fix_normal</span>
                            <span class="text-sm text-on-surface">${name}</span>
                        </div>
                        <span class="text-xs font-semibold bg-primary/10 text-primary px-2 py-0.5 rounded-full">${count}x</span>
                    </div>`).join('');
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

            document.querySelectorAll('.period-btn').forEach(btn =>
                btn.addEventListener('click', () => {
                    currentPeriod = btn.dataset.period;
                    localStorage.setItem('dashboard_period', currentPeriod);
                    updateDashboard();
                })
            );

            document.querySelectorAll('.chart-btn').forEach(btn =>
                btn.addEventListener('click', () => {
                    currentChart = btn.dataset.chart;
                    localStorage.setItem('dashboard_chart', currentChart);
                    renderChart(currentPeriod, currentChart);
                    setActive('.chart-btn', currentChart, 'chart');
                })
            );

            updateDashboard();
        </script>
    @endpush
</x-app-layout>
