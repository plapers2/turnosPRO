<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfessionalAvailabilitiesSeeder extends Seeder
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
        $users = DB::table('users')->pluck('id');

        foreach ($users as $userId) {
            foreach ($days as $day) {
                DB::table('professional_availabilities')->insert([
                    'day_of_week' => $day,
                    'start_time' => '08:00:00',
                    'end_time' => '18:00:00',
                    'user_id' => $userId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
