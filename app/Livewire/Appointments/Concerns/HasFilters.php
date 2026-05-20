<?php

namespace App\Livewire\Appointments\Concerns;

use App\Models\Service;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

trait HasFilters
{
    #[Url(as: 'q', keep: false)]
    public string $search             = '';

    #[Url(as: 'professional', keep: false)]
    public ?int   $filterProfessional = null;

    #[Url(as: 'status', keep: false)]
    public string $filterStatus       = '';

    #[Url(as: 'from', keep: false)]
    public string $filterDateFrom     = '';

    #[Url(as: 'to', keep: false)]
    public string $filterDateTo       = '';

    #[Url(as: 'service', keep: false)]
    public string $filterService      = '';

    // ── Watchers ──────────────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function updatedFilterDateFrom(): void
    {
        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function updatedFilterDateTo(): void
    {
        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function updatedFilterProfessional(): void
    {
        if (! $this->isAdmin) {
            $this->filterProfessional = null;
        }

        // Resetear servicio si el nuevo profesional no lo tiene
        $this->filterService = '';

        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function updatedFilterService(): void
    {
        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function resetFilters(): void
    {
        $this->search         = '';
        $this->filterStatus   = '';
        $this->filterDateFrom = '';
        $this->filterDateTo   = '';
        $this->filterService  = '';

        if ($this->isAdmin) {
            $this->filterProfessional = null;
        }

        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    // ── Computed: servicios disponibles según profesional activo ─────────────
    #[Computed]
    public function filterableServices(): Collection
    {
        $companyId = session('active_company_id');

        $base = Service::query()
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->orderBy('name');

        if ($this->isAdmin && ! empty($this->filterProfessional)) {
            // Solo servicios del profesional seleccionado
            $base->whereHas('users', fn($q) => $q->where('users.id', $this->filterProfessional));
        } elseif (! $this->isAdmin) {
            // Profesional no-admin → solo los suyos
            $base->whereHas('users', fn($q) => $q->where('users.id', auth()->id()));
        }
        // Admin sin filtro → todos los de la empresa

        return $base->get(['id', 'name', 'duration']);
    }
}
