<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

class EmpleadoDashboard extends Component
{
    use HasDashboardData;

    public string $period    = 'hoy';
    public string $chartType = 'barras';

    // El userId se pasa a todos los sub-componentes para que filtren sus queries
    public int $userId;

    public function mount(): void
    {
        $this->period    = session('dashboard_period', 'hoy');
        $this->chartType = session('dashboard_chart', 'barras');
        $this->userId    = auth()->id();
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        session(['dashboard_period' => $period]);
        $this->dispatch('period-changed', period: $period);
    }

    public function setChartType(string $type): void
    {
        $this->chartType = $type;
        session(['dashboard_chart' => $type]);
        $this->dispatch('chart-type-changed', chartType: $type);
    }

    public function render()
    {
        return view('livewire.dashboard.admin-dashboard', [
            'period'    => $this->period,
            'chartType' => $this->chartType,
            'userId'    => $this->userId,   // ← se pasa al blade para los @livewire
        ]);
    }
}
