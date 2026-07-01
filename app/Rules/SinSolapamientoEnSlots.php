<?php
// app/Rules/SinSolapamientoEnSlots.php
namespace App\Rules;

use Closure;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;

class SinSolapamientoEnSlots implements ValidationRule
{
    public function __construct(
        private string $diaKey,
        private int    $indexActual,
        private array  $todosLosSlots
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // end_time del slot actual (puede estar vacío aún si no pasó su validación)
        $finActual = $this->todosLosSlots[$this->indexActual]['end_time'] ?? null;
        if (!$finActual) {
            return; // end_time inválido: su propia regla ya reportará el error
        }

        $nuevoInicio = Carbon::createFromFormat('H:i', substr($value, 0, 5));
        $nuevoFin    = Carbon::createFromFormat('H:i', substr($finActual, 0, 5));

        if ($nuevoInicio->gte($nuevoFin)) {
            return; // la regla HoraFinPosteriorAInicio ya lo cubre
        }

        foreach ($this->todosLosSlots as $j => $slot) {
            // Solo comparar slots del mismo día, saltando el propio
            if ($j === $this->indexActual || ($slot['day_of_week'] ?? '') !== $this->diaKey) {
                continue;
            }

            $otroInicio = $slot['start_time'] ?? null;
            $otroFin    = $slot['end_time']   ?? null;

            if (!$otroInicio || !$otroFin) {
                continue; // slot incompleto, sus propias reglas lo reportarán
            }

            try {
                $oInicio = Carbon::createFromFormat('H:i', substr($otroInicio, 0, 5));
                $oFin    = Carbon::createFromFormat('H:i', substr($otroFin, 0, 5));
            } catch (\Exception) {
                continue;
            }

            // Misma condición de solapamiento: A < D && C < B
            if ($nuevoInicio->lt($oFin) && $oInicio->lt($nuevoFin)) {
                $fail(
                    "El turno {$nuevoInicio->format('H:i')}–{$nuevoFin->format('H:i')} " .
                        "se choca con otro turno del mismo día " .
                        "({$oInicio->format('H:i')}–{$oFin->format('H:i')})."
                );
                return;
            }
        }
    }
}
