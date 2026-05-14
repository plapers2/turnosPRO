<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $tipo = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }
    public function updatingTipo(): void
    {
        $this->resetPage();
    }

    public function restoreCompany(int $id): void
    {
        Company::withTrashed()->findOrFail($id)->restore();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteCompany(int $id): void
    {
        $company = Company::findOrFail($id);

        $activeCitas = Appointment::where('company_id', $company->id)
            ->whereIn('status', [
                Appointment::STATUS_PENDING,
                Appointment::STATUS_CONFIRMED,
            ])
            ->count();

        if ($activeCitas > 0) {
            $this->dispatch('company-has-active-appointments', count: $activeCitas);
            return;
        }

        $company->delete();
        $this->dispatch('company-deleted');
    }

    public function render()
    {
        abort_unless(auth()->user()->hasRole('master'), 403);
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');
        $tipos = \App\Models\TypeCompany::orderBy('name')->pluck('name', 'id');

        $companies = Company::withTrashed()
            ->with('typeCompany')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->status === 'active', fn($q) => $q->whereNull('deleted_at'))
            ->when($this->status === 'inactive', fn($q) => $q->onlyTrashed())
            ->when($this->tipo, fn($q) => $q->where('type_company_id', $this->tipo))
            ->paginate(10);

        return view('livewire.companies.company-index', compact('companies', 'tipos'));
    }
}
