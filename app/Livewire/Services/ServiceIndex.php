<?php

namespace App\Livewire\Services;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
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

    // ── Estadísticas (conteos globales, sin filtros) ──────────────────────────
    public function getActiveCountProperty(): int
    {
        $companyId = session('active_company_id');
        return Service::where('company_id', $companyId)->count(); // sin trashed
    }

    public function getInactiveCountProperty(): int
    {
        $companyId = session('active_company_id');
        return Service::onlyTrashed()->where('company_id', $companyId)->count();
    }

    public function getAvgDurationProperty(): int
    {
        $companyId = session('active_company_id');
        return (int) round(Service::where('company_id', $companyId)->avg('duration') ?? 0);
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
        $companyId = session('active_company_id');

        // Verificar si el servicio tiene citas activas (pending o confirmed)
        $hasActiveAppointments = Appointment::where('company_id', $companyId)
            ->whereHas('services', function ($q) use ($id) {
                $q->where('service_id', $id);
            })
            ->where(function ($q) {
                $q->where('status', Appointment::STATUS_PENDING)
                    ->orWhere('status', Appointment::STATUS_CONFIRMED);
            })
            ->exists();

        if ($hasActiveAppointments) {
            // Lanzar error o notificar al usuario
            $this->dispatch(
                'delete-error',
                message: 'El servicio actual tiene citas activas.'
            );
            return;
        }

        // Verificar si el servicio tiene profesionales asociados
        $hasActiveProfessionals = User::whereHas('companies', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
            ->whereHas('services', function ($q) use ($id) {
                $q->where('service_id', $id);
            })
            ->exists();

        if ($hasActiveProfessionals) {
            // Lanzar error o notificar al usario
            $this->dispatch('delete-error', message: "El servicio actual tiene profesionales asociados a el");
            return;
        }


        Service::where('id', $id)
            ->where('company_id', $companyId) // seguridad: validar que pertenece a la empresa
            ->firstOrFail()
            ->delete(); // SoftDelete por el trait

        $this->dispatch('service-deleted');
    }

    public function render()
    {
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');
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
            ->paginate(8);

        return view('livewire.services.⚡service-index', [
            'services'      => $services,
            'activeCount'   => $this->activeCount,
            'inactiveCount' => $this->inactiveCount,
            'avgDuration'   => $this->avgDuration,
        ]);
    }
}
