<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        // Limpiar imágenes anteriores
        Storage::disk('public')->deleteDirectory('users');
        Storage::disk('public')->makeDirectory('users');

        // Usuarios base con firstOrCreate para evitar duplicados
        $master = User::firstOrCreate(
            ['email' => 'master@gmail.com'],
            [
                'name'     => 'Master',
                'password' => Hash::make('12345'),
                'phone'    => '000000000',
                'image'    => null,
            ]
        );
        
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('12345'),
                'phone'    => '123456789',
                'image'    => $this->downloadImage(),
            ]
        );

        $empleado = User::firstOrCreate(
            ['email' => 'empleado@gmail.com'],
            [
                'name'     => 'Empleado',
                'password' => Hash::make('12345'),
                'phone'    => '987654321',
                'image'    => $this->downloadImage(),
            ]
        );

        $cliente = User::firstOrCreate(
            ['email' => 'cliente@gmail.com'],
            [
                'name'     => 'Cliente',
                'password' => Hash::make('12345'),
                'phone'    => '0192837465',
                'image'    => $this->downloadImage(),
            ]
        );

        // Asignar roles a usuarios base
        $master->assignRole('master');
        $admin->assignRole('admin');
        $empleado->assignRole('empleado');
        $cliente->assignRole('cliente');

        // Usuarios adicionales con factory
        $names = [
            'Gaelan',
            'Orelle',
            'Veronika',
            'Gelya',
            'Xymenes',
            'Sande',
            'Clarey',
            'Ami',
            'Caitlin',
            'Barbi',
            'Stuart',
            'Renata',
            'Karine',
            'Lynde',
            'Cherice',
            'Gerda',
            'Leann',
        ];

        $faker = \Faker\Factory::create();
        foreach ($names as $name) {
            $user = User::firstOrCreate(
                ['email' => $faker->unique()->safeEmail()],
                [
                    'name'     => $name,
                    'password' => Hash::make('password123'),
                    'phone'    => $faker->phoneNumber(),
                    'image'    => $this->downloadImage(),
                ]
            );
            $user->assignRole('empleado');
        }
    }

    private function downloadImage(): ?string
    {
        $localImages = glob(database_path('seeders/images/users/*.jpg'));
        if (!empty($localImages)) {
            $source = $localImages[array_rand($localImages)];
            $filename = 'users/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, file_get_contents($source));
            return $filename;
        }
        return null;
    }
}
