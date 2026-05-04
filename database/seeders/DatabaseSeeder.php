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

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
            SpecialAppointmentSeeder::class
            // Demostracion en feria del software:
            // php artisan migrate:fresh --seed --seeder=DemoOnlySeeder
        ]);
    }
}
