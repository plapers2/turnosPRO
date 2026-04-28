<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OpeningHourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ];

        $companies = DB::table('companies')->pluck('id');

        if ($companies->isEmpty()) {
            return;
        }

        foreach ($companies as $companyId) {
            foreach ($days as $day) {
                DB::table('opening_hours')->insert([
                    'day_of_week' => $day,
                    'start_time' => '08:00:00',
                    'end_time' => '18:00:00',
                    'company_id' => $companyId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
