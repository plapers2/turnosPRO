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
            'email' => "admin@gmail.com",
            'password' => Hash::make("12345"),
            'phone' => "123456789",
            'image' => "https://placehold.co/600x400"
        ]);
        // User 2
        DB::table('users')->insert([
            'name' => "Empleado",
            'email' => "empleado@gmail.com",
            'password' => Hash::make("54321"),
            'phone' => "987654321",
            'image' => "https://placehold.co/600x400"
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
            "Elsie"
        ];
        // Users 3 - cantidad de arreglo (en este caso 18, por ende 20 usuarios en total)
        $faker = Faker::create();
        foreach ($users as $user) {
            DB::table('users')->insert([
                'name' => $user,
                'email' => $faker->email(),
                'password' => Hash::make('password123'),
                'phone' => $faker->phoneNumber(),
                'image' => "https://placehold.co/600x400"
            ]);
        }

        // Asignacion de roles
        $admin = User::find(1);
        $admin->assignRole('admin');

        for ($i = 2; $i < 20; $i++) {
            $empleado = User::find($i);
            $empleado->assignRole('empleado');
        }
    }
}
