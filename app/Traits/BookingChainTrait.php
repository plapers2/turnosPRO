<?php

namespace App\Traits;

use Carbon\Carbon;

trait BookingChainTrait
{
    protected function verificarCadenaConsecutiva(
        $services,
        $profesionales,
        $citasExistentes,
        Carbon $slotInicio,
        string $dayOfWeek
    ): int {
        $contador = 0;
        $this->contarCadenas(
            0,
            $services,
            $profesionales,
            $citasExistentes,
            $slotInicio,
            $dayOfWeek,
            [],
            $contador
        );
        return $contador;
    }

    protected function contarCadenas(
        int $index,
        $services,
        $profesionales,
        $citasExistentes,
        Carbon $cursor,
        string $dayOfWeek,
        array $asignados,
        int &$contador
    ): void {
        if ($index >= $services->count()) {
            $contador++;
            return;
        }

        $servicio   = $services[$index];
        $inicio     = $cursor->copy();
        $fin        = $inicio->copy()->addMinutes($servicio->duration);
        $horaIniStr = $inicio->format('H:i:s');
        $horaFinStr = $fin->format('H:i:s');

        foreach ($profesionales as $prof) {
            if (!$prof->services->contains('id', $servicio->id)) continue;
            if (in_array($prof->id, $asignados)) continue;

            $tieneHorario = $prof->professionalAvailabilities->contains(
                fn($pa) =>
                $pa->day_of_week === $dayOfWeek
                    && $pa->start_time <= $horaIniStr
                    && $pa->end_time   >= $horaFinStr
            );
            if (!$tieneHorario) continue;

            $ocupado = $citasExistentes->contains(
                fn($cita) =>
                Carbon::parse($cita->start_time) < $fin
                    && Carbon::parse($cita->end_time) > $inicio
                    && $cita->user_id === $prof->id
            );
            if ($ocupado) continue;

            $this->contarCadenas(
                $index + 1,
                $services,
                $profesionales,
                $citasExistentes,
                $fin,
                $dayOfWeek,
                [...$asignados, $prof->id],
                $contador
            );
        }
    }
}
