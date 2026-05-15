<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

#[Lazy]
class OcupacionChart extends Component
{
    use HasDashboardData;

    public string $period    = 'hoy';
    public string $chartType = 'barras';

    // Opcional: filtra por empleado cuando se pasa
    public ?int $userId = null;

    public function mount(string $period = 'hoy', string $chartType = 'barras', ?int $userId = null): void
    {
        $this->period    = $period;
        $this->chartType = $chartType;
        $this->userId    = $userId;
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

    public function setChartType(string $type): void
    {
        $this->chartType = $type;
        session(['dashboard_chart' => $type]);
        $this->dispatch('chart-type-changed', chartType: $type);
        $this->pushChart();
    }

    public function pushChart(): void
    {
        $data = $this->buildChartData();
        $this->dispatch('chart-data-updated', payload: $data[$this->chartType]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="h-[340px] rounded-2xl bg-surface-container animate-pulse"></div>
        HTML;
    }

    public function render()
    {
        return view('livewire.dashboard.ocupacion-chart', [
            'chartData' => $this->buildChartData(),
        ]);
    }
}
