<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\User;

class ServiceUserSeeder extends Seeder
{
    /**
     * Lógica de negocio:
     * - Cada Service pertenece a una Company (company_id).
     * - Los Users (profesionales) también pertenecen a Companies via company_user.
     * - Solo los profesionales que pertenecen a la MISMA empresa del servicio
     *   pueden ser asignados a ese servicio (service_user).
     * - El BookingController filtra profesionales por:
     *   1. Que pertenezcan a la company  → whereHas('companies')
     *   2. Que tengan el servicio asignado → whereHas('services')
     *   3. Que tengan disponibilidad horaria ese día
     *   4. Que no tengan conflicto de citas
     */
    public function run(): void
    {
        $services = Service::all();
        $users = User::with(['companies', 'roles'])->get();

        if ($services->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay servicios o usuarios. Ejecuta sus seeders primero.');
            return;
        }

        foreach ($services as $service) {
            // Solo profesionales de la misma empresa que el servicio
            $profesionalesEmpresa = $users->filter(function (User $user) use ($service) {
                return $user->companies->contains('id', $service->company_id) && $user->hasRole('empleado');
            });

            if ($profesionalesEmpresa->isEmpty()) {
                $this->command->warn("El servicio [{$service->name}] no tiene profesionales en su empresa (company_id: {$service->company_id}). Omitiendo.");
                continue;
            }

            // Asignar entre 1 y todos los profesionales disponibles de esa empresa
            $cantidad         = rand(1, min(3, $profesionalesEmpresa->count()));
            $seleccionados    = $profesionalesEmpresa->random($cantidad);

            foreach ($seleccionados as $user) {
                DB::table('service_user')->insertOrIgnore([
                    'service_id' => $service->id,
                    'user_id'    => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('ServiceUserSeeder ejecutado correctamente.');
    }
}
