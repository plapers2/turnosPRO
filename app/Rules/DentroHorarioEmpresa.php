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
        private int    $companyId,
        private bool   $validarDia = true  // ← nuevo parámetro
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
            // Solo mostrar este error en hora_inicio
            if ($this->validarDia) {
                $traducciones = [
                    'monday'    => 'lunes',
                    'tuesday'   => 'martes',
                    'wednesday' => 'miércoles',
                    'thursday'  => 'jueves',
                    'friday'    => 'viernes',
                    'saturday'  => 'sábado',
                    'sunday'    => 'domingo',
                ];
                $diaLegible = $traducciones[$this->diaKey] ?? $this->diaKey;
                $fail("La empresa no atiende los {$diaLegible}.");
            }
            return;  // ← en ambos casos corta aquí
        }

        $valor    = Carbon::createFromFormat('H:i', substr($value, 0, 5));
        $apertura = Carbon::createFromFormat('H:i', substr($horario->start_time, 0, 5));
        $cierre   = Carbon::createFromFormat('H:i', substr($horario->end_time, 0, 5));

        if ($valor->lt($apertura) || $valor->gt($cierre)) {
            $fail("El horario debe estar entre {$apertura->format('H:i')} y {$cierre->format('H:i')}.");
        }
    }
}
