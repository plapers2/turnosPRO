<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Livewire\Dashboard\Concerns\HasDashboardData;

#[Lazy]
class KpiCards extends Component
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
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 animate-pulse">
            <div class="h-[72px] rounded-xl bg-surface-container"></div>
            <div class="h-[72px] rounded-xl bg-surface-container"></div>
            <div class="h-[72px] rounded-xl bg-surface-container"></div>
            <div class="h-[72px] rounded-xl bg-surface-container"></div>
        </div>
        HTML;
    }

    public function render()
    {
        $data = $this->buildKpis();

        return view('livewire.dashboard.kpi-cards', [
            'kpis' => $data['cards'],
        ]);
    }
}
