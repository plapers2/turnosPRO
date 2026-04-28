<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\UserSeeder;
use Database\Seeders\TypeCompanySeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\AppointmentSeeder;
use Database\Seeders\ProfessionalAvailabilitiesSeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\OpeningHourSeeder;
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
            ProfessionalAvailabilitiesSeeder::class,
            OpeningHourSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
