<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

class KpiCards extends Component
{
    use HasDashboardData;

    public string $period = 'hoy';

    // Recibe el período inicial desde el componente padre vía :period="$period"
    public function mount(string $period = 'hoy'): void
    {
        $this->period = $period;
    }

    #[On('period-changed')]
    public function updatePeriod(string $period): void
    {
        $this->period = $period;
    }

    public function render()
    {
        $data = $this->buildKpis();

        return view('livewire.dashboard.kpi-cards', [
            'kpis'       => $data['cards'],        // ← solo las 4 tarjetas
            'asistencia' => $data['asistencia'],   // ← separado para el blade
        ]);
    }
}
