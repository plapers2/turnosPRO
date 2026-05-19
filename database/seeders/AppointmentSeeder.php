<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Company;

class AppointmentSeeder extends Seeder
{
    /**
     * Lógica de negocio:
     * - El user_id (profesional) DEBE pertenecer a la company_id de la cita.
     * - El profesional DEBE tener al menos un servicio asignado en esa empresa (service_user).
     * - El customer_id DEBE pertenecer a la misma empresa.
     */
    public function run(): void
    {
        $companies = Company::with('users')->get();

        if ($companies->isEmpty()) {
            $this->command->warn('No hay empresas. Ejecuta CompanySeeder primero.');
            return;
        }

        $inserted = 0;

        foreach ($companies as $company) {
            // Profesionales de esta empresa que tengan servicios asignados
            $profesionales = $company->users()
                ->whereHas('services', fn($q) => $q->where('services.company_id', $company->id))
                ->get();

            if ($profesionales->isEmpty()) {
                $this->command->warn("Empresa [{$company->name}]: sin profesionales con servicios asignados. Omitiendo.");
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

            // Crear 2 citas por empresa
            for ($i = 0; $i < 2; $i++) {
                $profesional   = $profesionales->random();
                $appointmentDate = Carbon::now()->addDays(rand(1, 10));
                $startHour     = rand(8, 17);
                $startTime     = $appointmentDate->copy()->setTime($startHour, 0);
                $endTime       = $startTime->copy()->addHour();

                DB::table('appointments')->insert([
                    'start_time'             => $startTime,
                    'end_time'               => $endTime,
                    'cancellation_reason'    => null,
                    'payment_expires_at'     => '23:59:00',
                    'status'                 => 'confirmed',
                    'notes'                  => 'Cita de prueba generada por seeder',
                    'cancel_token'           => uniqid(),
                    'cancel_token_expires_at' => Carbon::now()->addDays(2),
                    'customer_id'            => $customerIds->random(),
                    'user_id'                => $profesional->id,   // profesional de la empresa
                    'company_id'             => $company->id,       // empresa correcta
                    'created_at'             => Carbon::now(),
                    'updated_at'             => Carbon::now(),
                ]);

                $inserted++;
            }
        }

        $this->command->info("AppointmentSeeder ejecutado correctamente. {$inserted} citas creadas.");
    }
}
