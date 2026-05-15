<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

#[Lazy]
class TasaAsistencia extends Component
{
    use HasDashboardData;

    public string $period = 'hoy';

    // Opcional: filtra por empleado cuando se pasa
    public ?int $userId = null;

    public function mount(string $period = 'hoy', ?int $userId = null): void
    {
        $this->period = $period;
        $this->userId = $userId;
    }

    #[On('period-changed')]
    public function updatePeriod(string $period): void
    {
        $this->period = $period;
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="h-40 rounded-2xl bg-surface-container animate-pulse"></div>
        HTML;
    }

    public function render()
    {
        $data = $this->buildKpis();

        $this->dispatch('tasa-updated');

        return view('livewire.dashboard.tasa-asistencia', [
            'asistencia' => $data['asistencia'],
        ]);
    }
}
