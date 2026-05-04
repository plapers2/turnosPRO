<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoOnlySeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // php artisan migrate:fresh --seed --seeder=DemoOnlySeeder
            RolesAndPermissionsSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
