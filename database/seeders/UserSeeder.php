<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::table('users')->insert([
            'name' => "Administrador",
            'email' => "admin@gmail.com",
            'password' => Hash::make("12345"),
            'phone' => "123456789",
            'image' => "imagen.png"
        ]);

        DB::table('users')->insert([
            'name' => "Empleado",
            'email' => "empleado@gmail.com",
            'password' => Hash::make("54321"),
            'phone' => "987654321",
            'image' => "img.png"
        ]);

        $admin = User::find(1);
        $admin->assignRole('admin');

        $empleado = User::find(2);
        $empleado->assignRole('empleado');
    }
}
