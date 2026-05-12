<?php

namespace App\Livewire\Companies;

use App\Models\TypeCompany;
use Livewire\Component;
use Livewire\WithPagination;

class TypeCompanyIndex extends Component
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

    public function restoreTypeCompany(int $id): void
    {
        TypeCompany::withTrashed()->findOrFail($id)->restore();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteTypeCompany(int $id): void
    {
        TypeCompany::findOrFail($id)->delete();
        $this->dispatch('type-company-deleted');
    }

    public function render()
    {
        abort_unless(auth()->user()->hasRole('master'), 403);
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');
        \Illuminate\Pagination\Paginator::currentPathResolver(function () {
            return url('/master/type-companies');
        });

        $typeCompanies = TypeCompany::withTrashed()
            ->withCount('companies')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->status === 'active', fn($q) => $q->whereNull('deleted_at'))
            ->when($this->status === 'inactive', fn($q) => $q->onlyTrashed())
            ->paginate(10);

        return view('livewire.companies.type-company-index', compact('typeCompanies'));
    }
}
