<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

class ProximasCitas extends Component
{
    use HasDashboardData;

    // No depende del período — siempre muestra las próximas desde ahora
    public string $period = 'hoy';

    public function render()
    {
        return view('livewire.dashboard.proximas-citas', [
            'appointments' => $this->buildAppointments(),
        ]);
    }
}
