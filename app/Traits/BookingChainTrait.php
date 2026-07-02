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

    /**
     * Construye la asignación real de profesionales para un slot concreto,
     * usando el mismo backtracking que verificarCadenaConsecutiva/contarCadenas
     * en lugar de un greedy por orden de servicio.
     *
     * Para cada servicio, un candidato solo se incluye si, además de estar
     * calificado/disponible/libre, existe una forma de completar los
     * servicios restantes sin quedarse sin nadie (se valida recursivamente
     * con contarCadenas). Si tras ese filtro queda exactamente 1 candidato,
     * se auto-asigna, pero ahora sin bloquear servicios posteriores que
     * dependían de ese profesional.
     *
     * @return array [service_id => ['service'=>..., 'hora_inicio'=>..., 'hora_fin'=>..., 'profesionales'=>Collection, 'auto_asignado'=>?int]]
     */
    protected function construirAsignacion(
        $services,
        $profesionales,
        $citasExistentes,
        Carbon $cursorInicio,
        string $dayOfWeek
    ): array {
        $resultado = [];
        $asignados = [];
        $cursor    = $cursorInicio->copy();
        $total     = $services->count();

        foreach ($services as $index => $servicio) {
            $inicio     = $cursor->copy();
            $fin        = $inicio->copy()->addMinutes($servicio->duration);
            $horaIniStr = $inicio->format('H:i:s');
            $horaFinStr = $fin->format('H:i:s');

            $candidatos = [];

            foreach ($profesionales as $prof) {
                if (in_array($prof->id, $asignados)) continue;
                if (!$prof->services->contains('id', $servicio->id)) continue;

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

                if ($index + 1 >= $total) {
                    // Último servicio: no hay nada más que validar.
                    $candidatos[] = $prof;
                    continue;
                }

                // ¿Asignar a $prof aquí sigue permitiendo completar el resto?
                $restoContador = 0;
                $this->contarCadenas(
                    $index + 1,
                    $services,
                    $profesionales,
                    $citasExistentes,
                    $fin,
                    $dayOfWeek,
                    [...$asignados, $prof->id],
                    $restoContador
                );

                if ($restoContador > 0) {
                    $candidatos[] = $prof;
                }
            }

            $resultado[$servicio->id] = [
                'service' => [
                    'id'       => $servicio->id,
                    'name'     => $servicio->name,
                    'duration' => $servicio->duration,
                ],
                'hora_inicio'   => $inicio->format('H:i'),
                'hora_fin'      => $fin->format('H:i'),
                'profesionales' => collect($candidatos)->values(),
                'auto_asignado' => null,
            ];

            if (count($candidatos) === 1) {
                $resultado[$servicio->id]['auto_asignado'] = $candidatos[0]->id;
                $asignados[] = $candidatos[0]->id;
            }

            $cursor = $fin;
        }

        return $resultado;
    }
}
