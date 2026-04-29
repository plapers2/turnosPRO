<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;

class AppointmentServiceSeeder extends Seeder
{
    /**
     * Lógica de negocio:
     * - Cada Appointment tiene: user_id (profesional), company_id, customer_id,
     *   start_time y end_time.
     * - Los servicios vinculados a una cita DEBEN:
     *   1. Pertenecer a la misma empresa de la cita (service.company_id = appointment.company_id)
     *   2. Estar asignados al profesional de la cita (service_user)
     * - La duración total de los servicios determina el rango start_time → end_time.
     * - En el BookingController, cada cita guarda UN servicio por appointment
     *   (un appointment = un profesional + un servicio), aunque un "booking_group"
     *   puede agrupar múltiples appointments del mismo cliente.
     */
    public function run(): void
    {
        $appointments = Appointment::all();

        if ($appointments->isEmpty()) {
            $this->command->warn('No hay citas. Ejecuta AppointmentSeeder primero.');
            return;
        }

        foreach ($appointments as $appointment) {
            // Servicios válidos: misma empresa Y asignados al profesional de la cita
            $serviciosValidos = Service::where('company_id', $appointment->company_id)
                ->whereHas('users', fn($q) => $q->where('users.id', $appointment->user_id))
                ->get();

            if ($serviciosValidos->isEmpty()) {
                $this->command->warn("Cita ID {$appointment->id}: el profesional (user_id: {$appointment->user_id}) no tiene servicios asignados en la empresa. Omitiendo.");
                continue;
            }

            // Según la lógica del BookingController, cada appointment corresponde
            // a UN servicio cuya duración coincide con (end_time - start_time).
            $duracionCita = $appointment->start_time->diffInMinutes($appointment->end_time);

            // Intentar encontrar el servicio cuya duración coincida exactamente
            $servicioExacto = $serviciosValidos->first(
                fn($s) => (int) $s->duration === (int) $duracionCita
            );

            if ($servicioExacto) {
                DB::table('appointment_service')->insertOrIgnore([
                    'appointment_id' => $appointment->id,
                    'service_id'     => $servicioExacto->id,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } else {
                // Fallback: asignar un servicio aleatorio válido
                $servicio = $serviciosValidos->random();
                DB::table('appointment_service')->insertOrIgnore([
                    'appointment_id' => $appointment->id,
                    'service_id'     => $servicio->id,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        $this->command->info('AppointmentServiceSeeder ejecutado correctamente.');
    }
}
