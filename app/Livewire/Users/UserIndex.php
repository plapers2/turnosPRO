<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $role = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingStatus(): void
    {
        $this->resetPage();
    }
    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function restoreUser(int $id): void
    {
        User::withTrashed()->findOrFail($id)->restore();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteUser(int $id): void
    {
        User::findOrFail($id)->delete();
        $this->dispatch('user-deleted');
    }

    public function render()
    {
        $companyId = session('active_company_id');
        $roles = Role::all();
        $userId = auth()->id();


        $users = User::withTrashed()
            ->when($companyId, fn($q) => $q->whereHas('companies', fn($q) => $q->where('companies.id', $companyId)))
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->status === 'active', fn($q) => $q->whereNull('deleted_at'))
            ->when($this->status === 'inactive', fn($q) => $q->onlyTrashed())
            ->when($this->role, fn($q) => $q->whereHas('roles', fn($q) => $q->where('name', $this->role)))
            ->where('id', '!=', $userId)
            ->paginate(10);

        return view('livewire.users.⚡user-index', compact(['users', 'roles']));
    }
}
