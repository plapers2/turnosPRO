<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class ClientesMasivosSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = 1; // Cambia al ID de tu empresa activa

        $clientes = [
            ['name' => 'Ana García',      'email' => 'ana.garcia@gmail.com',      'phone' => '3001234501'],
            ['name' => 'Carlos Martínez', 'email' => 'carlos.martinez@gmail.com', 'phone' => '3001234502'],
            ['name' => 'Laura Rodríguez', 'email' => 'laura.rodriguez@gmail.com', 'phone' => '3001234503'],
            ['name' => 'Miguel Torres',   'email' => 'miguel.torres@hotmail.com', 'phone' => '3001234504'],
            ['name' => 'Sofía López',     'email' => 'sofia.lopez@gmail.com',     'phone' => '3001234505'],
            ['name' => 'Andrés Herrera',  'email' => 'andres.herrera@gmail.com',  'phone' => '3001234506'],
            ['name' => 'Valentina Díaz',  'email' => 'valentina.diaz@gmail.com',  'phone' => '3001234507'],
            ['name' => 'Julián Vargas',   'email' => 'julian.vargas@yahoo.com',   'phone' => '3001234508'],
            ['name' => 'Isabella Mora',   'email' => 'isabella.mora@gmail.com',   'phone' => '3001234509'],
            ['name' => 'Sebastián Ruiz',  'email' => 'sebastian.ruiz@gmail.com',  'phone' => '3001234510'],
            ['name' => 'Camila Castro',   'email' => 'camila.castro@gmail.com',   'phone' => '3001234511'],
            ['name' => 'Diego Jiménez',   'email' => 'diego.jimenez@hotmail.com', 'phone' => '3001234512'],
            ['name' => 'Mariana Ríos',    'email' => 'mariana.rios@gmail.com',    'phone' => '3001234513'],
            ['name' => 'Felipe Mendoza',  'email' => 'felipe.mendoza@gmail.com',  'phone' => '3001234514'],
            ['name' => 'Natalia Sánchez', 'email' => 'natalia.sanchez@gmail.com', 'phone' => '3001234515'],
            ['name' => 'Santiago Gómez',  'email' => 'santiago.gomez@gmail.com',  'phone' => '3001234516'],
            ['name' => 'Daniela Flores',  'email' => 'daniela.flores@yahoo.com',  'phone' => '3001234517'],
            ['name' => 'Mateo Guerrero',  'email' => 'mateo.guerrero@gmail.com',  'phone' => '3001234518'],
            ['name' => 'Gabriela Peña',   'email' => 'gabriela.pena@gmail.com',   'phone' => '3001234519'],
            ['name' => 'Samuel Romero',   'email' => 'samuel.romero@gmail.com',   'phone' => '3001234520'],
            ['name' => 'Luciana Ortiz',   'email' => 'luciana.ortiz@hotmail.com', 'phone' => '3001234521'],
            ['name' => 'Tomás Navarro',   'email' => 'tomas.navarro@gmail.com',   'phone' => '3001234522'],
            ['name' => 'Alejandra Silva', 'email' => 'alejandra.silva@gmail.com', 'phone' => '3001234523'],
            ['name' => 'Esteban Medina',  'email' => 'esteban.medina@gmail.com',  'phone' => '3001234524'],
            ['name' => 'Paula Aguilar',   'email' => 'paula.aguilar@gmail.com',   'phone' => '3001234525'],
        ];

        $inserted = 0;

        foreach ($clientes as $data) {
            // Saltar si el email ya existe
            if (User::where('email', $data['email'])->exists()) continue;

            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'],
                'password'          => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
            ]);

            $user->assignRole('cliente');

            DB::table('customers')->insert([
                'user_id'    => $user->id,
                'company_id' => $companyId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $inserted++;
        }

        $this->command->info("{$inserted} clientes creados y asociados a la empresa {$companyId}.");
    }
}
