<?php

namespace App\Livewire\ProfessionalAvailability;

use App\Models\ProfessionalAvailability;
use Carbon\Carbon;
use Livewire\Component;

class ProfessionalAvailabilityIndex extends Component
{
    public string $status = '';
    public string $day = '';

    public function render()
    {
        $companyId = session('active_company_id');
        $user = auth()->user();

        $days = [
            'monday'    => 'Lunes',
            'tuesday'   => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday'  => 'Jueves',
            'friday'    => 'Viernes',
            'saturday'  => 'Sábado',
            'sunday'    => 'Domingo',
        ];

        $query = ProfessionalAvailability::withTrashed()
            ->orderBy('deleted_at', 'asc')
            ->when($this->status === 'active', fn($q) => $q->whereNull('deleted_at'))
            ->when($this->status === 'inactive', fn($q) => $q->onlyTrashed())
            ->when(
                $this->day,
                fn($q) =>
                $q->whereRaw('LOWER(day_of_week) = ?', [strtolower($this->day)])
            );

        if ($user->hasRole('admin')) {

            $query->whereHas('user', function ($q) use ($companyId) {
                $q->whereHas('companies', function ($j) use ($companyId) {
                    $j->where('company_id', $companyId);
                });
            });
        } else {

            $query->whereHas('user', function ($q) use ($companyId) {
                $q->where('id', auth()->id())
                    ->whereHas('companies', function ($j) use ($companyId) {
                        $j->where('company_id', $companyId);
                    });
            });
        }

        $profesionalAvailability = $query
            ->orderByRaw("
        FIELD(day_of_week,
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday'
        )
    ")
            ->get()
            ->groupBy(fn($hour) => strtolower($hour->day_of_week));
        // Si hay filtro de día, solo mostrar ese día en el grid
        $visibleDays = $this->day ? [$this->day => $days[$this->day]] : $days;


        return view('livewire.professional-availabilities.⚡professional-availability-index', compact('profesionalAvailability', 'days', 'visibleDays'));
    }
}
