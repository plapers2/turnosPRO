<?php

namespace App\Livewire\ProfessionalAvailability;

use App\Models\ProfessionalAvailability;
use Carbon\Carbon;
use Livewire\Component;

class ProfessionalAvailabilityIndex extends Component
{
    public string $status = '';
    public string $day = '';
    public string $search = '';

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

        $query = ProfessionalAvailability::with(['user'])
            ->withTrashed()
            ->orderBy('deleted_at', 'asc')

            // FILTRO STATUS
            ->when(
                $this->status === 'active',
                fn($q) =>
                $q->whereNull('deleted_at')
            )

            ->when(
                $this->status === 'inactive',
                fn($q) =>
                $q->onlyTrashed()
            )

            // FILTRO DIA
            ->when(
                $this->day,
                fn($q) =>
                $q->whereRaw('LOWER(day_of_week) = ?', [strtolower($this->day)])
            )

            // FILTRO SEARCH
            ->when($this->search, function ($q) {

                $search = '%' . $this->search . '%';

                $q->whereHas('user', function ($userQuery) use ($search) {

                    $userQuery->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                });
            });

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
