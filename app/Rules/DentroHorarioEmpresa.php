<?php
// app/Rules/DentroHorarioEmpresa.php
namespace App\Rules;

use App\Models\HorarioEmpresa;
use App\Models\OpeningHour;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class DentroHorarioEmpresa implements ValidationRule
{
    public function __construct(
        private string $diaKey,
        private int    $companyId
    ) {}


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->companyId) {
            $fail("No se pudo determinar la empresa activa.");
            return;
        }

        $horario = OpeningHour::where('company_id', $this->companyId)
            ->where('day_of_week', $this->diaKey)
            ->first();

        if (!$horario) {
            $this->diaKey === "monday" ? $this->diaKey = "lunes" : $this->diaKey;
            $this->diaKey === "tuesday" ? $this->diaKey = "Martes" : $this->diaKey;
            $this->diaKey === "wednesday" ? $this->diaKey = "miercoles" : $this->diaKey;
            $this->diaKey === "thursday" ? $this->diaKey = "jueves" : $this->diaKey;
            $this->diaKey === "friday" ? $this->diaKey = "viernes" : $this->diaKey;
            $this->diaKey === "saturday" ? $this->diaKey = "Sabado" : $this->diaKey;
            $this->diaKey === "sunday" ? $this->diaKey = "Domingo" : $this->diaKey;

            $fail("La empresa no atiende los {$this->diaKey}.");
            return;
        }

        $valor    = Carbon::createFromFormat('H:i', substr($value, 0, 5));
        $apertura = Carbon::createFromFormat('H:i', substr($horario->start_time, 0, 5));
        $cierre   = Carbon::createFromFormat('H:i', substr($horario->end_time, 0, 5));

        if ($valor->lt($apertura) || $valor->gt($cierre)) {
            $fail("El horario debe estar entre {$apertura->format('H:i')} y {$cierre->format('H:i')}.");
        }
    }
}
