<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 20; $i++) {
            DB::table('company_user')->insert([
                'company_id' => ($i + 1),
                'user_id' => ($i + 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
