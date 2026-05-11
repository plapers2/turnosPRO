<?php

namespace App\Livewire\Appointments\Concerns;

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

trait HasCalendar
{
    public string $calendarMonth = '';

    public function bootHasCalendar(): void
    {
        $this->calendarMonth = now()->format('Y-m');
    }

    #[On('calendarGoToMonth')]
    public function onCalendarGoToMonth(string $month): void
    {
        $this->calendarMonth = $month;
        $this->refreshCalendarEvents();
    }

    #[On('calendarEventClicked')]
    public function onCalendarEventClicked(int|string $id): void
    {
        $this->viewAppointment($id);
    }

    protected function refreshCalendarEvents(): void
    {
        $this->dispatch('calendarEventsUpdated', events: $this->calendarEvents);
    }

    #[Computed]
    public function calendarEvents(): array
    {
        return $this->baseQuery()
            ->get()
            ->map(fn($a) => [
                'id'    => $a->id,
                'title' => ($a->customer?->name ?? 'Cliente eliminado') . ' · ' . $a->start_time->format('H:i'),
                'start' => $a->start_time->format('Y-m-d\TH:i:s'),
                'end'   => $a->end_time->format('Y-m-d\TH:i:s'),
                'backgroundColor' => match ($a->status) {
                    'confirmed' => '#1D9E75',
                    'cancelled' => '#E24B4A',
                    'completed' => '#378ADD',
                    default     => '#BA7517',
                },
                'borderColor' => match ($a->status) {
                    'confirmed' => '#0F6E56',
                    'cancelled' => '#A32D2D',
                    'completed' => '#185FA5',
                    default     => '#854F0B',
                },
                'textColor'     => '#ffffff',
                'extendedProps' => [
                    'professional' => $a->user?->name ?? 'Empleado eliminado',
                    'services'     => $a->services->pluck('name')->join(', ') ?: 'Servicio eliminado',
                    'status'       => $a->status,
                ],
            ])
            ->toArray();
    }
}
