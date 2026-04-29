<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Company;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->warn('No hay empresas. Ejecuta CompanySeeder primero.');
            return;
        }

        $total = 0;

        // Garantizar al menos 2 servicios por empresa
        foreach ($companies as $company) {
            $cantidad = rand(2, 3);
            Service::factory()->count($cantidad)->create([
                'company_id' => $company->id,
            ]);
            $total += $cantidad;
        }

        $this->command->info("ServicesSeeder ejecutado correctamente. {$total} servicios creados.");
    }
}
