<?php

namespace App\Livewire\Appointments\Concerns;

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
    public ?int   $filterService      = null;

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

        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function resetFilters(): void
    {
        $this->search         = '';
        $this->filterStatus   = '';
        $this->filterDateFrom = '';
        $this->filterDateTo   = '';
        $this->filterService  = null;

        if ($this->isAdmin) {
            $this->filterProfessional = null;
        }

        $this->resetPage();
        $this->refreshCalendarEvents();
    }

    public function updatedFilterService(): void
    {
        $this->resetPage();
        $this->refreshCalendarEvents();
    }
}
