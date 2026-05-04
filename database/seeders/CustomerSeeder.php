<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        foreach ($companies as $companyId) {
            for ($j = 0; $j < 2; $j++) {
                // Crear usuario
                $userId = DB::table('users')->insertGetId([
                    'name'              => 'Customer ' . $counter,
                    'email'             => 'customer' . $counter . '@gmail.com',
                    'password'          => Hash::make('12345'),
                    'phone'             => '300' . str_pad($counter, 7, '0', STR_PAD_LEFT),
                    'image'             => 'https://placehold.co/600x400',
                    'email_verified_at' => Carbon::now(),
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ]);

                // Asignar rol cliente
                $roleId = DB::table('roles')->where('name', 'cliente')->value('id');
                if ($roleId) {
                    DB::table('model_has_roles')->insert([
                        'role_id'    => $roleId,
                        'model_type' => 'App\\Models\\User',
                        'model_id'   => $userId,
                    ]);
                }

                // Crear customer vinculado al usuario
                DB::table('customers')->insert([
                    'user_id'    => $userId,
                    'company_id' => $companyId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $counter++;
            }
        }

        $this->command->info("CustomerSeeder ejecutado: {$counter} clientes creados.");
    }
}
