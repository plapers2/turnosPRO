<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Company;

class HistoricalAppointmentSeeder extends Seeder
{
    /**
     * Genera citas pasadas en estado 'completed' y 'cancelled'
     * para pruebas de reportes, filtros e historial.
     *
     * Requisitos:
     * - Requiere CompanySeeder, CustomerSeeder, ServiceUserSeeder previos.
     * - Ejecutable de forma aislada: php artisan db:seed --class=HistoricalAppointmentSeeder
     */
    public function run(): void
    {
        $companies = Company::with('users')->get();

        if ($companies->isEmpty()) {
            $this->command->warn('No hay empresas. Ejecuta CompanySeeder primero.');
            return;
        }

        $inserted = 0;

        // Motivos de cancelación realistas
        $motivosCancelacion = [
            'El cliente no se presentó.',
            'Cancelado por solicitud del cliente.',
            'Emergencia personal del cliente.',
            'El profesional no estaba disponible.',
            'Reagendado para otra fecha.',
        ];

        foreach ($companies as $company) {
            // Profesionales con servicios asignados en esta empresa
            $profesionales = $company->users()
                ->whereHas('services', fn($q) => $q->where('services.company_id', $company->id))
                ->get();

            if ($profesionales->isEmpty()) {
                $this->command->warn("Empresa [{$company->name}]: sin profesionales. Omitiendo.");
                continue;
            }

            // Servicios de la empresa con sus IDs
            $servicios = DB::table('services')
                ->where('company_id', $company->id)
                ->pluck('id');

            if ($servicios->isEmpty()) {
                $this->command->warn("Empresa [{$company->name}]: sin servicios. Omitiendo.");
                continue;
            }

            // Clientes de esta empresa
            $customerIds = DB::table('customers')
                ->where('company_id', $company->id)
                ->pluck('id');

            if ($customerIds->isEmpty()) {
                $this->command->warn("Empresa [{$company->name}]: sin clientes. Omitiendo.");
                continue;
            }

            // 5 completadas + 3 canceladas por empresa
            $lotes = [
                ['status' => 'completed', 'cantidad' => 5],
                ['status' => 'cancelled', 'cantidad' => 3],
            ];

            foreach ($lotes as $lote) {
                for ($i = 0; $i < $lote['cantidad']; $i++) {
                    $profesional = $profesionales->random();
                    $servicio    = $servicios->random();

                    // Fechas en el pasado — entre 1 y 120 días atrás
                    $diasAtras   = rand(1, 120);
                    $startHour   = rand(8, 17);
                    $startTime   = Carbon::now()->subDays($diasAtras)->setTime($startHour, 0);
                    $endTime     = $startTime->copy()->addHour();

                    $esCancelada = $lote['status'] === 'cancelled';

                    $appointmentId = DB::table('appointments')->insertGetId([
                        'start_time'             => $startTime,
                        'end_time'               => $endTime,
                        'status'                 => $lote['status'],
                        'cancellation_reason'    => $esCancelada
                            ? $motivosCancelacion[array_rand($motivosCancelacion)]
                            : null,
                        'completed_at'           => $esCancelada ? null : $endTime,
                        'cancelled_by'           => $esCancelada ? $profesional->id : null,
                        'completed_by'           => $esCancelada ? null : $profesional->id,
                        'payment_expires_at'     => '23:59:00',
                        'notes'                  => 'Cita histórica generada por seeder',
                        'cancel_token'           => uniqid(),
                        'cancel_token_expires_at' => Carbon::now()->subDays($diasAtras)->addDays(7),
                        'customer_id'            => $customerIds->random(),
                        'user_id'                => $profesional->id,
                        'company_id'             => $company->id,
                        'created_at'             => $startTime->copy()->subDays(rand(1, 7)),
                        'updated_at'             => $endTime,
                    ]);

                    // Asociar el servicio en appointment_service
                    DB::table('appointment_service')->insert([
                        'appointment_id' => $appointmentId,
                        'service_id'     => $servicio,
                    ]);

                    $inserted++;
                }
            }
        }

        $this->command->info("HistoricalAppointmentSeeder ejecutado. {$inserted} citas históricas creadas.");
    }
}
