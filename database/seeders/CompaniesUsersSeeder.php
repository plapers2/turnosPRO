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
    $users     = User::role('empleado')->get(); // ← solo empleados

    if ($companies->isEmpty() || $users->isEmpty()) {
        $this->command->warn('No hay empresas o usuarios empleados. Ejecuta sus seeders primero.');
        return;
    }

    $shuffledUsers = $users->shuffle();
    $userIndex     = 0;

    foreach ($companies as $company) {
        $usuarioFijo = $shuffledUsers[$userIndex % $shuffledUsers->count()];
        DB::table('company_user')->insertOrIgnore([
            'company_id' => $company->id,
            'user_id'    => $usuarioFijo->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $userIndex++;

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
