<?php

namespace App\Livewire\Appointments\Concerns;

trait HasFilters
{
    public string $search             = '';
    public ?int   $filterProfessional = null;
    public string $filterStatus       = '';
    public string $filterDateFrom     = '';
    public string $filterDateTo       = '';
    public ?int $filterService        = null;

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
