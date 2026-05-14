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

    public function mount(string $period = 'hoy'): void
    {
        $this->period = $period;
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

        return view('livewire.dashboard.tasa-asistencia', [
            'asistencia' => $data['asistencia'],
        ]);
    }
}
