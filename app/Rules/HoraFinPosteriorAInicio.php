<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class HoraFinPosteriorAInicio implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function __construct(
        private ?string $horaInicio,
        private string  $diaLegible,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->horaInicio || !$value) return;

        $inicio = Carbon::createFromFormat('H:i', substr($this->horaInicio, 0, 5));
        $fin    = Carbon::createFromFormat('H:i', substr($value, 0, 5));

        if ($fin->lte($inicio)) {
            $fail(
                "El turno del {$this->diaLegible} debe terminar después de las " .
                    "{$inicio->format('H:i')} (actualmente termina a las {$fin->format('H:i')})."
            );
        }
    }
}
