<?php

namespace App\Rules;

use App\Models\OpeningHour;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class DentroHorarioEmpresa implements ValidationRule
{
    public function __construct(
        private string $diaKey,
        private int $companyId,
        private bool $validarDia = true
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->companyId) {
            $fail("No se pudo determinar la empresa activa.");
            return;
        }

        $horarios = OpeningHour::where('company_id', $this->companyId)
            ->where('day_of_week', $this->diaKey)
            ->whereNull('deleted_at')
            ->get();

        // La empresa no trabaja ese día
        if ($horarios->isEmpty()) {
            if ($this->validarDia) {

                $traducciones = [
                    'Monday'    => 'lunes',
                    'Tuesday'   => 'martes',
                    'Wednesday' => 'miércoles',
                    'Thursday'  => 'jueves',
                    'Friday'    => 'viernes',
                    'Saturday'  => 'sábado',
                    'Sunday'    => 'domingo',
                ];

                $diaLegible = $traducciones[$this->diaKey] ?? $this->diaKey;

                $fail("La empresa no atiende los {$diaLegible}.");
            }

            return;
        }

        $valor = Carbon::createFromFormat('H:i', substr($value, 0, 5));

        // Verificar si cae dentro de algún rango permitido
        foreach ($horarios as $horario) {

            $apertura = Carbon::createFromFormat(
                'H:i',
                substr($horario->start_time, 0, 5)
            );

            $cierre = Carbon::createFromFormat(
                'H:i',
                substr($horario->end_time, 0, 5)
            );

            if ($valor->between($apertura, $cierre)) {
                return;
            }
        }

        // Construir mensaje legible
        $rangosMensaje = $horarios
            ->map(function ($h) {

                $inicio = Carbon::createFromFormat(
                    'H:i',
                    substr($h->start_time, 0, 5)
                )->format('H:i');

                $fin = Carbon::createFromFormat(
                    'H:i',
                    substr($h->end_time, 0, 5)
                )->format('H:i');

                return "{$inicio}–{$fin}";
            })
            ->join(', ', ' o ');

        // Detectar qué campo falló
        $campo = str_contains($attribute, 'hora_inicio')
            ? 'La hora de inicio'
            : 'La hora de fin';

        $fail("{$campo} debe estar dentro de los rangos permitidos: {$rangosMensaje}.");
    }
}
