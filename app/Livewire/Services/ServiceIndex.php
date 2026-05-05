<?php

namespace App\Livewire\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceIndex extends Component
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

    public function restoreService(int $id): void
    {
        Service::withTrashed()->findOrFail($id)->restore();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteService(int $id): void
    {
        Service::findOrFail($id)->delete();
        $this->dispatch('service-deleted');
    }

    public function render()
    {
        $companyId = session('active_company_id');

        $services = Service::withTrashed()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when(
                auth()->user()->hasRole('empleado'),
                fn($q) => $q->whereHas('users', function ($q) {
                    $q->where('users.id', auth()->id());
                })
            )
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->status === 'active', fn($q) => $q->whereNull('deleted_at'))
            ->when($this->status === 'inactive', fn($q) => $q->onlyTrashed())
            ->paginate(9);

        return view('livewire.services.⚡service-index  ', compact('services'));
    }
}
