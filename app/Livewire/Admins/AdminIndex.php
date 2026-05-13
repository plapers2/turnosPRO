<?php

namespace App\Livewire\Admins;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AdminIndex extends Component
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

    public function confirmDelete(int $id): void
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteAdmin(int $id): void
    {
        $admin = User::withTrashed()->findOrFail($id);
        $admin->delete();
        $this->dispatch('admin-deleted');
    }

    public function restoreAdmin(int $id): void
    {
        $admin = User::withTrashed()->findOrFail($id);
        $admin->restore();
    }

    public function render()
    {
        $admins = User::role('admin')
            ->withTrashed()
            ->when(
                $this->search,
                fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
            )
            ->when($this->status === 'active',   fn($q) => $q->whereNull('deleted_at'))
            ->when($this->status === 'inactive', fn($q) => $q->whereNotNull('deleted_at'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admins.admin-index', compact('admins'));
    }
}
