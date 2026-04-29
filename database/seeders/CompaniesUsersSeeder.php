<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\User;

class CompaniesUsersSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $users     = User::all();

        if ($companies->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay empresas o usuarios. Ejecuta sus seeders primero.');
            return;
        }

        // Mezclar usuarios para distribuirlos de forma más uniforme
        $shuffledUsers = $users->shuffle();
        $userIndex     = 0;

        foreach ($companies as $company) {
            // Garantizar al menos 1 usuario por empresa (asignación rotativa)
            $usuarioFijo = $shuffledUsers[$userIndex % $shuffledUsers->count()];
            DB::table('company_user')->insertOrIgnore([
                'company_id' => $company->id,
                'user_id'    => $usuarioFijo->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $userIndex++;

            // Agregar entre 1 y 2 usuarios extra aleatorios
            $extras = $users->random(min(rand(1, 2), $users->count()));
            foreach ($extras as $user) {
                DB::table('company_user')->insertOrIgnore([
                    'company_id' => $company->id,
                    'user_id'    => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('CompaniesUsersSeeder ejecutado correctamente.');
    }
}
