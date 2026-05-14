<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

class ServiciosSolicitados extends Component
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
        return view('livewire.dashboard.servicios-solicitados', [
            'services' => $this->buildServices(),
        ]);
    }
}
