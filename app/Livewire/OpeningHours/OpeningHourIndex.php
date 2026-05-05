<?php

namespace App\Livewire\OpeningHours;

use App\Models\OpeningHour;
use Livewire\Component;

class OpeningHourIndex extends Component
{
    public string $status = '';
    public string $day = '';

    public function restoreHour(int $id): void
    {
        OpeningHour::withTrashed()->findOrFail($id)->restore();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteHour(int $id): void
    {
        OpeningHour::findOrFail($id)->delete();
        $this->dispatch('hour-deleted');
    }

    public function render()
    {
        $companyId = session('active_company_id');

        $days = [
            'monday'    => 'Lunes',
            'tuesday'   => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday'  => 'Jueves',
            'friday'    => 'Viernes',
            'saturday'  => 'Sábado',
            'sunday'    => 'Domingo',
        ];

        $openingHours = OpeningHour::withTrashed()
            ->where('company_id', $companyId)
            ->when($this->status === 'active',   fn($q) => $q->whereNull('deleted_at'))
            ->when($this->status === 'inactive', fn($q) => $q->onlyTrashed())
            ->when($this->day, fn($q) => $q->whereRaw('LOWER(day_of_week) = ?', [$this->day]))
            ->orderByRaw("FIELD(day_of_week,
                'monday','tuesday','wednesday','thursday','friday','saturday','sunday'
            )")
            ->get()
            ->groupBy(fn($hour) => strtolower($hour->day_of_week));
        // Si hay filtro de día, solo mostrar ese día en el grid
        $visibleDays = $this->day ? [$this->day => $days[$this->day]] : $days;

        return view('livewire.opening-hours.⚡opening-hour-index', compact('openingHours', 'days', 'visibleDays'));
    }
}
