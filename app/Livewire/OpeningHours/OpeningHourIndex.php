<?php

namespace App\Livewire\OpeningHours;

use App\Models\OpeningHour;
use App\Models\ProfessionalAvailability;
use Carbon\Carbon;
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
        $companyId = session('active_company_id');

        $hour = OpeningHour::where('id', $id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        // Buscar disponibilidades que se solapen con este horario
        $conflictingAvailabilities = ProfessionalAvailability::with('user')
            ->where('day_of_week', $hour->day_of_week)
            ->where('start_time', '<', $hour->end_time)
            ->where('end_time', '>', $hour->start_time)
            ->whereHas('user.companies', function ($q) use ($companyId) {
                $q->where('companies.id', $companyId);
            })
            ->get();

        foreach ($conflictingAvailabilities as $availability) {
            if (!$this->isFullyCovered($availability, $hour->id, $companyId)) {
                // Disparar error al frontend con SweetAlert
                $this->dispatch(
                    'delete-error',
                    message: 'No puedes eliminar este horario porque el profesional "'
                        . $availability->user->name
                        . '" tiene disponibilidad que quedaría fuera del horario permitido.'
                );
                return;
            }
        }

        $hour->delete();
        $this->dispatch('hour-deleted');
    }

    private function isFullyCovered(
        ProfessionalAvailability $availability,
        int $excludeHourId,
        int $companyId
    ): bool {
        $availStart = Carbon::createFromTimeString($availability->start_time);
        $availEnd   = Carbon::createFromTimeString($availability->end_time);

        $hours = OpeningHour::where('company_id', $companyId)
            ->where('id', '!=', $excludeHourId)
            ->where('day_of_week', $availability->day_of_week)
            ->where('start_time', '<', $availability->end_time)
            ->where('end_time', '>', $availability->start_time)
            ->orderBy('start_time')
            ->get();

        if ($hours->isEmpty()) {
            return false;
        }

        $covered = clone $availStart;

        foreach ($hours as $h) {
            $hStart = Carbon::createFromTimeString($h->start_time);
            $hEnd   = Carbon::createFromTimeString($h->end_time);

            if ($hStart->gt($covered)) {
                return false;
            }

            if ($hEnd->gt($covered)) {
                $covered = clone $hEnd;
            }

            if ($covered->gte($availEnd)) {
                return true;
            }
        }

        return $covered->gte($availEnd);
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
            ->orderBy("deleted_at", 'asc')
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
