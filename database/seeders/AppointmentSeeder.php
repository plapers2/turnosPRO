<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $customers = DB::table('customers')->pluck('id');
        $users = DB::table('users')->pluck('id');
        $companies = DB::table('companies')->pluck('id');

        // if ($customers->isEmpty() || $users->isEmpty() || $companies->isEmpty()) {
        //     return;
        // }

        foreach ($customers as $customerId) {
            $appointmentDate = Carbon::now()->addDays(rand(1, 10));

            $startTime = $appointmentDate->copy()->setTime(9, 0);
            $endTime = $appointmentDate->copy()->setTime(10, 0);

            DB::table('appointments')->insert([
                'start_time' => $startTime,
                'end_time' => $endTime,
                'cancellation_reason' => null,
                'payment_expires_at' => '23:59:00',
                'notes' => 'Scheduled appointment',
                'cancel_token' => uniqid(),
                'cancel_token_expires_at' => Carbon::now()->addDays(2),
                'customer_id' => $customerId,
                'user_id' => $users->random(),
                'company_id' => $companies->random(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
