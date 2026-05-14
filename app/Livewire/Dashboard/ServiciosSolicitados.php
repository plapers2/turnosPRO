<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

#[Lazy]
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

    public function placeholder()
    {
        return <<<'HTML'
        <div class="h-48 rounded-2xl bg-surface-container animate-pulse"></div>
        HTML;
    }

    public function render()
    {
        $this->dispatch('servicios-updated');

        return view('livewire.dashboard.servicios-solicitados', [
            'services' => $this->buildServices(),
        ]);
    }
}
