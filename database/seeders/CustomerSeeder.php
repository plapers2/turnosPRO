<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $companies = DB::table('companies')->pluck('id');

        if ($companies->isEmpty()) {
            $this->command->warn('No hay empresas. Ejecuta CompanySeeder primero.');
            return;
        }

        $counter = 1;

        // Garantizar al menos 2 clientes por empresa
        foreach ($companies as $companyId) {
            for ($j = 0; $j < 2; $j++) {
                DB::table('customers')->insert([
                    'name'       => 'Customer ' . $counter,
                    'email'      => 'customer' . $counter . '@gmail.com',
                    'phone'      => '300' . str_pad($counter, 7, '0', STR_PAD_LEFT),
                    'company_id' => $companyId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $counter++;
            }
        }

        $this->command->info("CustomerSeeder ejecutado correctamente. {$counter} clientes creados.");
    }
}
