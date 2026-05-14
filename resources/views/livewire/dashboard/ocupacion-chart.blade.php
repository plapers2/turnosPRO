<div x-data="{
    chart: null,
    chartType: @entangle('chartType'),

    destroyChart() {
        // Destruye por referencia Y por cualquier instancia registrada en el canvas
        if (this.chart) {
            this.chart.destroy();
            this.chart = null;
        }
        // Limpia instancias huérfanas que Livewire morph deja en el canvas
        if (this.$refs.canvas) {
            const existing = Chart.getChart(this.$refs.canvas);
            if (existing) existing.destroy();
        }
    },

    makeConfig(data, isLine) {
        return {
            type: isLine ? 'line' : 'bar',
            data: {
                labels: data.labels,
                datasets: data.datasets.map(ds => isLine ? { ...ds, backgroundColor: 'transparent', tension: 0.4, pointRadius: 3 } : { ...ds, borderRadius: 8 }),
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 300 },
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } },
                },
            },
        };
    },

    buildChart() {
        if (!this.$refs.canvas) return;
        this.destroyChart();
        const raw = @js($chartData);
        const isLine = this.chartType === 'lineas';
        this.chart = new Chart(this.$refs.canvas.getContext('2d'), this.makeConfig(raw[this.chartType], isLine));
    },

    updateChart(payload) {
        if (!this.$refs.canvas) return;
        this.destroyChart();
        const isLine = this.chartType === 'lineas';
        this.chart = new Chart(this.$refs.canvas.getContext('2d'), this.makeConfig(payload, isLine));
    },

    init() {
        this.$nextTick(() => this.buildChart());
        this.$wire.on('chart-data-updated', ({ payload }) => {
            this.$nextTick(() => this.updateChart(payload));
        });
    },
}">
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/20 shadow-sm">
        <div class="flex justify-between items-center mb-5">
            <h2 class="font-semibold text-primary flex items-center gap-2">
                <span class="material-symbols-rounded ms-outline" style="font-size:1.1rem;">bar_chart</span>
                Ocupación
            </h2>
            <div class="flex gap-1 bg-surface-container rounded-full p-1 border border-outline-variant/40">
                <button wire:click="setChartType('barras')"
                    class="chart-btn {{ $chartType === 'barras' ? 'btn-active' : '' }}">
                    <span class="material-symbols-rounded ms-outline" style="font-size:.9rem;">bar_chart</span>
                    Barras
                </button>
                <button wire:click="setChartType('lineas')"
                    class="chart-btn {{ $chartType === 'lineas' ? 'btn-active' : '' }}">
                    <span class="material-symbols-rounded ms-outline" style="font-size:.9rem;">show_chart</span>
                    Líneas
                </button>
            </div>
        </div>
        <div class="h-64">
            <canvas x-ref="canvas"></canvas>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
@endpush
