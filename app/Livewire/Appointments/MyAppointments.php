<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use App\Mail\AppointmentCancelledAdminMail;
use Illuminate\Support\Facades\Mail;

class MyAppointments extends Component
{
    use WithPagination;

    public string $activeTab     = 'proximas'; // 'proximas' | 'historial'
    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterDateFrom = '';
    public string $filterDateTo  = '';

    public bool $showCancelModal = false;
    public ?int $cancelId        = null;
    public string $cancelReason  = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }
    public function updatingFilterDateFrom(): void
    {
        $this->resetPage();
    }
    public function updatingFilterDateTo(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->reset(['search', 'filterStatus', 'filterDateFrom', 'filterDateTo']);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterStatus', 'filterDateFrom', 'filterDateTo']);
        $this->resetPage();
    }

    public function openCancelModal(int $id): void
    {
        $this->cancelId       = $id;
        $this->cancelReason   = '';
        $this->showCancelModal = true;
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->cancelId        = null;
        $this->cancelReason    = '';
    }

    public function confirmCancel(): void
    {
        $user        = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        $appt = Appointment::whereIn('customer_id', $customerIds)
            ->with(['company.users'])
            ->findOrFail($this->cancelId);

        abort_unless(
            $appt->status === 'confirmed'
                && $appt->start_time->copy()->subHours(2)->gt(now()),
            403
        );

        $appt->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $this->cancelReason ?: null,
            'cancelled_by'        => auth()->id(),
        ]);

        $appt->company->users
            ->filter(fn($u) => $u->hasRole('admin'))
            ->each(
                fn($admin) => Mail::to($admin->email)
                    ->send(new AppointmentCancelledAdminMail($appt))
            );

        $this->closeCancelModal();
        $this->resetPage();
    }

    public function render()
    {
        $user        = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        $base = Appointment::whereIn('customer_id', $customerIds);

        $stats = [
            'total'     => (clone $base)->count(),
            'confirmed' => (clone $base)->where('status', 'confirmed')->where('start_time', '>=', now())->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
            'no_attend'   => (clone $base)->where('status', 'no_attend')->count(),
        ];

        $countProximas  = (clone $base)->where('status', 'confirmed')->where('start_time', '>=', now())->count();
        $countHistorial = (clone $base)->whereIn('status', ['completed', 'cancelled', 'no_attend    '])->count();

        $activeStatuses = $this->activeTab === 'proximas'
            ? ['confirmed']
            : ['completed', 'cancelled', 'no_attend'];

        $query = Appointment::whereIn('customer_id', $customerIds)
            ->whereIn('status', $activeStatuses)
            ->when($this->activeTab === 'proximas', fn($q) => $q->where('start_time', '>=', now()))
            ->with(['services', 'user', 'company'])
            ->when(
                $this->search,
                fn($q) => $q->where(
                    fn($q) => $q->whereHas('company', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('services', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                )
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('start_time', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('start_time', '<=', $this->filterDateTo))
            ->orderBy('start_time', $this->activeTab === 'proximas' ? 'asc' : 'desc');

        $appointments = $query->paginate(10);

        return view('livewire.appointments.cliente.my-appointments', compact(
            'stats',
            'appointments',
            'countProximas',
            'countHistorial'
        ));
    }
}
