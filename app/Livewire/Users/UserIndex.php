<?php

namespace App\Livewire\Users;

use App\Models\Appointment;
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

    public function getActiveCountProperty(): int
    {
        $companyId = session('active_company_id');
        return User::whereHas('companies', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->count(); // sin trashed
    }

    public function getInactiveCountProperty(): int
    {
        $companyId = session('active_company_id');
        return User::onlyTrashed()->whereHas('companies', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->count();
    }

    public function restoreUser(int $id): void
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        $user->professionalAvailabilities()->onlyTrashed()->restore();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteUser(int $id): void
    {
        $companyId = session('active_company_id');
        $hasActiveAppointments = Appointment::where('company_id', $companyId)
            ->where('user_id', $id)
            ->whereIn('status', [Appointment::STATUS_CONFIRMED, Appointment::STATUS_PENDING])
            ->exists();
        if ($hasActiveAppointments) {
            $this->dispatch('delete-error', message: "El usuario actual tiene citas activas o pendientes");
            return;
        }

        $user = User::findOrFail($id);

        // Solo hace soft delete (NO borra imagen)
        $user->professionalAvailabilities()->delete();
        $user->delete();

        $this->dispatch('user-deleted');
    }

    public function render()
    {
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');
        $companyId = session('active_company_id');
        $roles = Role::all();
        $userId = auth()->id();


        $users = User::withTrashed()
            ->orderBy("deleted_at", 'asc')
            ->when($companyId, fn($q) => $q->whereHas('companies', fn($q) => $q->where('companies.id', $companyId)))
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->status === 'active', fn($q) => $q->whereNull('deleted_at'))
            ->when($this->status === 'inactive', fn($q) => $q->onlyTrashed())
            ->when(
                $this->role,
                fn($q) => $q->whereHas('roles', fn($q) => $q->where('name', $this->role)),
                fn($q) => $q->whereHas('roles', fn($q) => $q->where('name', '!=', 'cliente'))
            )
            ->where('id', '!=', $userId)
            ->paginate(10);

        return view('livewire.users.⚡user-index', [
            'users' => $users,
            'roles' => $roles,
            'activeCount' => $this->activeCount,
            'inactiveCount' => $this->inactiveCount
        ]);
    }
}
