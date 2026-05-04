<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        // User 1
        DB::table('users')->insert([
            'name' => "Administrador",
            'email' => "admin2@gmail.com",
            'password' => Hash::make("12345"),
            'phone' => "123456789",
        ]);
        // User 2
        DB::table('users')->insert([
            'name' => "Empleado",
            'email' => "empleado2@gmail.com",
            'password' => Hash::make("12345"),
            'phone' => "987654321",
        ]);
        // User 3
        DB::table('users')->insert([
            'name' => "Cliente",
            'email' => "cliente2@gmail.com",
            'password' => Hash::make("12345"),
            'phone' => "0192837465",
        ]);
        $users = [
            "Gaelan",
            "Orelle",
            "Veronika",
            "Gelya",
            "Xymenes",
            "Sande",
            "Clarey",
            "Ami",
            "Caitlin",
            "Barbi",
            "Stuart",
            "Renata",
            "Karine",
            "Lynde",
            "Cherice",
            "Gerda",
            "Leann",
        ];
        // Users 3 - cantidad de arreglo (en este caso 17 + 3 usuarios base, por ende 20 usuarios en total)
        $faker = Faker::create();
        foreach ($users as $user) {
            DB::table('users')->insert([
                'name' => $user,
                'email' => $faker->email(),
                'password' => Hash::make('password123'),
                'phone' => $faker->phoneNumber(),
            ]);
        }

        // Asignacion de roles
        $admin = User::find(1);
        $admin->assignRole('admin');

        $admin = User::find(2);
        $admin->assignRole('empleado');

        $admin = User::find(3);
        $admin->assignRole('cliente');

        // Si se cambia la cantidad de usuarios en total se cambia el limite del ciclo, OJO!
        for ($i = 4; $i < 20; $i++) {
            $empleado = User::find($i);
            $empleado->assignRole('empleado');
        }
    }
}
