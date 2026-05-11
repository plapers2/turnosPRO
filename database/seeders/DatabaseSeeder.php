<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\TypeCompanySeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ServicesSeeder;
use Database\Seeders\CompaniesUsersSeeder;
use Database\Seeders\ServiceUserSeeder;
use Database\Seeders\ProfessionalAvailabilitiesSeeder;
use Database\Seeders\OpeningHourSeeder;
use Database\Seeders\AppointmentSeeder;
use Database\Seeders\AppointmentServiceSeeder;
use Database\Seeders\DemoSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('logos');
        Storage::disk('public')->deleteDirectory('services');
        Storage::disk('public')->makeDirectory('logos');
        Storage::disk('public')->makeDirectory('services');
        Storage::disk('public')->deleteDirectory('users');
        Storage::disk('public')->makeDirectory('users');
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            TypeCompanySeeder::class,
            CompanySeeder::class,
            CustomerSeeder::class,
            ServicesSeeder::class,
            CompaniesUsersSeeder::class,
            ServiceUserSeeder::class,
            ProfessionalAvailabilitiesSeeder::class,
            OpeningHourSeeder::class,
            AppointmentSeeder::class,
            AppointmentServiceSeeder::class,
            // Demostracion en feria del software:
            // php artisan migrate:fresh --seed --seeder=DemoOnlySeeder
            // citas en dashboard
            //  php artisan db:seed --class=SpecialAppointmentSeeder
        ]);
    }
}
