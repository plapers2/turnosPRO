<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

class OcupacionChart extends Component
{
    use HasDashboardData;

    public string $period    = 'hoy';
    public string $chartType = 'barras';

    public function mount(string $period = 'hoy', string $chartType = 'barras'): void
    {
        $this->period    = $period;
        $this->chartType = $chartType;
    }

    #[On('period-changed')]
    public function updatePeriod(string $period): void
    {
        $this->period = $period;
        $this->pushChart();
    }

    #[On('chart-type-changed')]
    public function updateChartType(string $chartType): void
    {
        $this->chartType = $chartType;
        $this->pushChart();
    }

    // Envía los nuevos datos al JS de Chart.js vía dispatch al browser
    public function pushChart(): void
    {
        $data = $this->buildChartData();
        $this->dispatch('chart-data-updated', payload: $data[$this->chartType]);
    }

    public function render()
    {
        return view('livewire.dashboard.ocupacion-chart', [
            'chartData' => $this->buildChartData(),
        ]);
    }
}
