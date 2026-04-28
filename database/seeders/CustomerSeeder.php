<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = DB::table('companies')->pluck('id');

        if ($companies->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= 20; $i++) {
            DB::table('customers')->insert([
                'name' => 'Customer ' . $i,
                'email' => 'customer' . $i . '@gmail.com',
                'phone' => '30000000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'company_id' => $companies->random(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
