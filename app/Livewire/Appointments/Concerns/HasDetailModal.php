<?php

namespace App\Livewire\Appointments\Concerns;

use App\Models\Appointment;

trait HasDetailModal
{
    public bool         $showModal    = false;
    public ?Appointment $selectedAppt = null;

    public function viewAppointment(int $id): void
    {
        $query = $this->scopeCompany(
            Appointment::with([
                'customer' => fn($q) => $q->withTrashed(),
                'user'     => fn($q) => $q->withTrashed(),
                'services' => fn($q) => $q->withTrashed(),
            ])
        )->when(! $this->isAdmin, fn($q) => $q->where('user_id', auth()->id()));

        $this->selectedAppt = $query->findOrFail($id);
        $this->showModal    = true;
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal');
        $this->js("setTimeout(() => \$wire.call('destroyModal'), 160)");
    }

    public function destroyModal(): void
    {
        $this->showModal    = false;
        $this->selectedAppt = null;
    }

    protected function refreshSelectedAppt(Appointment $appointment): void
    {
        if ($this->showModal && $this->selectedAppt?->id === $appointment->id) {
            $this->selectedAppt = $appointment->fresh([
                'customer' => fn($q) => $q->withTrashed(),
                'user'     => fn($q) => $q->withTrashed(),
                'services' => fn($q) => $q->withTrashed(),
            ]);
        }
    }
}
