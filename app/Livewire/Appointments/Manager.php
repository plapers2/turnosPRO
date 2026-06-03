<?php

namespace App\Livewire\Appointments;

use App\Livewire\Appointments\Concerns\HasAppointmentActions;
use App\Livewire\Appointments\Concerns\HasAuthorization;
use App\Livewire\Appointments\Concerns\HasAvailabilitySlots;
use App\Livewire\Appointments\Concerns\HasCalendar;
use App\Livewire\Appointments\Concerns\HasDetailModal;
use App\Livewire\Appointments\Concerns\HasFilters;
use App\Livewire\Appointments\Concerns\HasProfessionalReassignment;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Appointments\Concerns\HasDelayNotification;

class Manager extends Component
{
    use WithPagination;
    use HasAuthorization;
    use HasDetailModal;
    use HasCalendar;
    use HasAppointmentActions;
    use HasProfessionalReassignment;
    use HasDelayNotification;
    use HasFilters,
        HasAvailabilitySlots {
        // ── filterService ──────────────────────────────────────────────────
        HasFilters::updatedFilterService           insteadof HasAvailabilitySlots;
        HasFilters::updatedFilterService           as updatedFilterServiceFilters;
        HasAvailabilitySlots::updatedFilterService as updatedFilterServiceAvailability;

        // ── filterProfessional ─────────────────────────────────────────────
        HasFilters::updatedFilterProfessional           insteadof HasAvailabilitySlots;
        HasFilters::updatedFilterProfessional           as updatedFilterProfessionalFilters;
        HasAvailabilitySlots::updatedFilterProfessional as updatedFilterProfessionalAvailability;
    }

    // 'list' | 'calendar' | 'availability'
    public string $view = 'list';
    public bool $showPendingBanner = true;

    public function updatedView(string $value): void
    {
        $this->dispatch('viewChanged', view: $value);
    }

    /**
     * Al cambiar servicio: resetear paginación + calendario (HasFilters)
     * y sincronizar slotMinutes (HasAvailabilitySlots).
     */
    public function updatedFilterService($value): void
    {
        $this->updatedFilterServiceFilters($value);
        $this->updatedFilterServiceAvailability($value);
    }

    /**
     * Al cambiar profesional: resetear paginación + calendario (HasFilters)
     * y resetear servicio/slotMinutes (HasAvailabilitySlots).
     */
    public function updatedFilterProfessional($value): void
    {
        $this->updatedFilterProfessionalFilters($value);
        $this->updatedFilterProfessionalAvailability($value);
    }

    private function baseQuery()
    {
        return $this->scopeCompany(
            Appointment::with([
                'customer' => fn($q) => $q->withTrashed(),
                'user'     => fn($q) => $q->withTrashed(),
                'services' => fn($q) => $q->withTrashed(),
            ])
        )
            ->when(! $this->isAdmin, fn($q) => $q->where('user_id', auth()->id()))
            ->when($this->search, fn($q) => $q->where(
                fn($inner) => $inner
                    ->whereHas('customer', fn($c) => $c->withTrashed()->whereHas('user', fn($u) => $u->withTrashed()->where('users.name', 'like', "%{$this->search}%")))
                    ->orWhereHas('user', fn($u) => $u->withTrashed()->where('users.name', 'like', "%{$this->search}%"))
                    ->orWhereHas('services', fn($s) => $s->withTrashed()->where('services.name', 'like', "%{$this->search}%"))
            ))
            ->when($this->isAdmin && $this->filterProfessional, fn($q) => $q->where('user_id', $this->filterProfessional))
            ->when($this->isAdmin && $this->filterService, fn($q) => $q->whereHas('services', fn($s) => $s->where('services.id', $this->filterService)))
            ->when($this->filterStatus,   fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('start_time', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo,   fn($q) => $q->whereDate('start_time', '<=', $this->filterDateTo));
    }

    #[Computed]
    public function appointments()
    {
        $now = now();

        return $this->baseQuery()
            ->orderByRaw("
                CASE WHEN start_time >= ? THEN 0 ELSE 1 END,
                CASE WHEN start_time >= ? THEN start_time ELSE NULL END ASC,
                CASE WHEN start_time < ? THEN start_time ELSE NULL END DESC
            ", [$now, $now, $now])
            ->paginate(8);
    }

    #[Computed]
    public function stats(): array
    {
        $counts = $this->baseQuery()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total'     => $counts->sum(),
            'confirmed' => $counts->get('confirmed', 0),
            'cancelled' => $counts->get('cancelled', 0),
            'completed' => $counts->get('completed', 0),
            'no_attend' => $counts->get('no_attend', 0)
        ];
    }

    #[Computed]
    public function professionals()
    {
        if (! $this->isAdmin) return collect();

        return User::withTrashed()
            ->role('empleado')
            ->when($this->companyId, fn($q) => $q->whereHas('companies', fn($c) => $c->where('companies.id', $this->companyId)))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function services()
    {
        if (! $this->isAdmin) return collect();

        return \App\Models\Service::where('company_id', $this->companyId)
            ->orderBy('name')
            ->get();
    }

    public function showConfirmAppointmentsForCompleted()
    {
        return $this->scopeCompany(
            Appointment::with([
                'customer' => fn($q) => $q->withTrashed(),
                'user'     => fn($q) => $q->withTrashed(),
                'services' => fn($q) => $q->withTrashed()
            ])
        )
            ->when(! $this->isAdmin, fn($q) => $q->where('user_id', auth()->id()))
            ->where('status', 'confirmed')
            ->whereDate('start_time', today())
            ->whereTime('start_time', '<', now())
            ->get();
    }

    public function dismissBanner(): void
    {
        $this->showPendingBanner = false;
    }

    public function render()
    {
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');

        return view('livewire.appointments.⚡manager', [
            'appointments'             => $this->appointments(),
            'stats'                    => $this->stats(),
            'professionals'            => $this->professionals(),
            'services'                 => $this->services(),
            'calendarEvents'           => $this->calendarEvents(),
            'isAdmin'                  => $this->isAdmin,
            'appointmentsForConfirmed' => $this->showConfirmAppointmentsForCompleted(),
            'availabilityDays'         => $this->availabilityDays(),
            'availabilitySummary'      => $this->availabilitySummary(),
            'availableServices'        => $this->availableServices(),
            // ── Usado por la filterbar de lista y calendario ──
            'filterableServices'       => $this->filterableServices(),
        ]);
    }
}
