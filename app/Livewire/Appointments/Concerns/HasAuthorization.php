<?php

namespace App\Livewire\Appointments\Concerns;

use App\Models\Appointment;
use Livewire\Attributes\On;

trait HasAuthorization
{
    public bool $isAdmin    = false;
    public bool $isEmployee = false;
    public ?int $companyId  = null;

    public function bootHasAuthorization(): void
    {
        $user             = auth()->user();
        $this->isAdmin    = $user->hasRole('admin');
        $this->isEmployee = $user->hasRole('empleado');
        $this->companyId  = session('active_company_id');
    }

    #[On('active-company-changed')]
    public function onCompanyChanged(): void
    {
        $this->companyId = session('active_company_id');
        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    protected function scopeCompany($query)
    {
        return $query->when(
            $this->companyId,
            fn($q) => $q->whereHas(
                'company',
                fn($c) => $c->where('companies.id', $this->companyId)
            )
        );
    }

    protected function authorizeAppointmentAction(int $id): void
    {
        $appt = Appointment::findOrFail($id);

        if ($this->companyId) {
            abort_if(
                ! $appt->company()->where('companies.id', $this->companyId)->exists(),
                403,
                'Esta cita no pertenece a la empresa activa.'
            );
        }

        if ($this->isAdmin) return;

        abort_if(
            $appt->user_id !== auth()->id(),
            403,
            'No tienes permiso para modificar esta cita.'
        );
    }
}
