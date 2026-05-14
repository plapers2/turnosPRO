<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Livewire\Dashboard\Concerns\HasDashboardData;
use App\Models\Appointment;

class EmpleadoDashboard extends Component
{
    use HasDashboardData;

    public string $period    = 'hoy';
    public string $chartType = 'barras';

    public function mount(): void
    {
        $this->period    = session('dashboard_period', 'hoy');
        $this->chartType = session('dashboard_chart', 'barras');
    }

    // ─── Única diferencia real con AdminDashboard ─────────────
    protected function baseQuery()
    {
        $companyId = session('active_company_id')
            ?? auth()->user()->companies()->first()?->id;

        abort_if(is_null($companyId), 403, 'No tienes una empresa asignada.');

        return Appointment::forCompany($companyId)
            ->where('user_id', auth()->id());
    }

    protected function applyUserFilter(): bool
    {
        return true;
    }
    // ──────────────────────────────────────────────────────────

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
        ]);
    }
}
