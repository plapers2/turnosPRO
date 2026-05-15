<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

class ProximasCitas extends Component
{
    use HasDashboardData;

    public string $period = 'hoy';

    // Opcional: filtra por empleado cuando se pasa
    public ?int $userId = null;

    public function mount(?int $userId = null): void
    {
        $this->userId = $userId;
    }

    public function render()
    {
        return view('livewire.dashboard.proximas-citas', [
            'appointments' => $this->buildAppointments(),
        ]);
    }
}
