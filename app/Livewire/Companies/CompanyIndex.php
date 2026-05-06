<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
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
        Company::findOrFail($id)->delete();
        $this->dispatch('company-deleted');
    }

    public function render()
    {
        $companies = Company::withTrashed()
            ->with('typeCompany')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->status === 'active', fn($q) => $q->whereNull('deleted_at'))
            ->when($this->status === 'inactive', fn($q) => $q->onlyTrashed())
            ->paginate(10);

        return view('livewire.companies.company-index', compact('companies'));
    }
}
