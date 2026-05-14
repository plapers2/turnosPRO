<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

class TasaAsistencia extends Component
{
    use HasDashboardData;

    public string $period = 'hoy';

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
        $kpis = $this->buildKpis();

        return view('livewire.dashboard.tasa-asistencia', [
            'asistencia' => $kpis['asistencia'],
        ]);
    }
}
