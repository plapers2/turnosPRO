<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SpecialAppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------
        // 1. USUARIOS
        // -------------------------------------------------------
        $adminId = DB::table('users')->insertGetId([
            'name'       => 'Administrador',
            'email'      => 'admin@gmail.com',
            'password'   => Hash::make('12345'),
            'phone'      => '123456789',
            'state'      => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $empleadoId = DB::table('users')->insertGetId([
            'name'       => 'Empleado',
            'email'      => 'empleado@gmail.com',
            'password'   => Hash::make('12345'),
            'phone'      => '987654321',
            'state'      => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $clienteId = DB::table('users')->insertGetId([
            'name'       => 'Cliente',
            'email'      => 'cliente@gmail.com',
            'password'   => Hash::make('12345'),
            'phone'      => '0192837465',
            'state'      => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::find($adminId)->assignRole('admin');
        User::find($empleadoId)->assignRole('empleado');
        User::find($clienteId)->assignRole('cliente');

        // -------------------------------------------------------
        // 2. TYPE_COMPANIES
        // -------------------------------------------------------
        $typeBeautyId = DB::table('type_companies')->insertGetId([
            'name'       => 'Belleza & Estética',
            'logo'       => 'type_companies/belleza.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $typeWellnessId = DB::table('type_companies')->insertGetId([
            'name'       => 'Bienestar & Salud',
            'logo'       => 'type_companies/bienestar.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // -------------------------------------------------------
        // 3. EMPRESAS
        // -------------------------------------------------------
        $mainCompanyId = DB::table('companies')->insertGetId([
            'name'            => 'Estudio Glamour',
            'logo'            => 'logos/glamour.png',
            'email'           => 'contacto@estudioglamour.com',
            'address'         => 'Calle 15 #8-42, Pereira',
            'phone'           => '3101234567',
            'type_company_id' => $typeBeautyId,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $secondCompanyId = DB::table('companies')->insertGetId([
            'name'            => 'Spa Sereno',
            'logo'            => 'logos/spa_sereno.png',
            'email'           => 'info@spasereno.com',
            'address'         => 'Carrera 10 #22-15, Pereira',
            'phone'           => '3209876543',
            'type_company_id' => $typeWellnessId,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $thirdCompanyId = DB::table('companies')->insertGetId([
            'name'            => 'Centro Vital',
            'logo'            => 'logos/centrovital.png',
            'email'           => 'hola@centrovital.com',
            'address'         => 'Av 30 de Agosto #40-10, Pereira',
            'phone'           => '3155557788',
            'type_company_id' => $typeWellnessId,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // -------------------------------------------------------
        // 4. COMPANY_USER
        //    Admin → las 3 empresas
        //    Empleado → solo Estudio Glamour (misma que admin)
        // -------------------------------------------------------
        DB::table('company_user')->insert([
            ['user_id' => $adminId,    'company_id' => $mainCompanyId,   'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $adminId,    'company_id' => $secondCompanyId, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $adminId,    'company_id' => $thirdCompanyId,  'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $empleadoId, 'company_id' => $mainCompanyId,   'created_at' => now(), 'updated_at' => now()],
        ]);

        // -------------------------------------------------------
        // 5. HORARIOS DE APERTURA
        // -------------------------------------------------------
        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day) {
            DB::table('opening_hours')->insert([
                'day_of_week' => $day,
                'start_time' => '08:00:00',
                'end_time' => '18:00:00',
                'company_id' => $mainCompanyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day) {
            foreach ([$secondCompanyId, $thirdCompanyId] as $cid) {
                DB::table('opening_hours')->insert([
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                    'company_id' => $cid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // -------------------------------------------------------
        // 6. DISPONIBILIDAD DEL EMPLEADO
        // -------------------------------------------------------
        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day) {
            DB::table('professional_availabilities')->insert([
                'day_of_week' => $day,
                'start_time' => '08:00:00',
                'end_time' => '18:00:00',
                'user_id' => $empleadoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------------------------------------
        // 7. SERVICIOS
        // -------------------------------------------------------
        $svcsMain = [
            ['name' => 'Corte de cabello', 'description' => 'Corte profesional personalizado.',            'duration' => 30,  'price' => 25000.00],
            ['name' => 'Tinte completo',   'description' => 'Coloración completa con productos premium.', 'duration' => 90,  'price' => 85000.00],
            ['name' => 'Manicure',         'description' => 'Cuidado y esmaltado de uñas.',               'duration' => 45,  'price' => 30000.00],
            ['name' => 'Pedicure',         'description' => 'Tratamiento completo de pies.',              'duration' => 60,  'price' => 35000.00],
            ['name' => 'Limpieza facial',  'description' => 'Limpieza profunda e hidratación.',           'duration' => 60,  'price' => 55000.00],
        ];
        $mainServiceIds = [];
        foreach ($svcsMain as $s) {
            $mainServiceIds[] = DB::table('services')->insertGetId(array_merge($s, [
                'image' => 'services/default.png',
                'company_id' => $mainCompanyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $svcsSecond = [
            ['name' => 'Masaje relajante', 'description' => 'Masaje corporal descontracturante.',            'duration' => 60, 'price' => 70000.00],
            ['name' => 'Aromaterapia',     'description' => 'Sesión de relajación con aceites esenciales.', 'duration' => 45, 'price' => 50000.00],
            ['name' => 'Reflexología',     'description' => 'Masaje terapéutico en pies y manos.',          'duration' => 45, 'price' => 55000.00],
        ];
        $secondServiceIds = [];
        foreach ($svcsSecond as $s) {
            $secondServiceIds[] = DB::table('services')->insertGetId(array_merge($s, [
                'image' => 'services/default.png',
                'company_id' => $secondCompanyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $svcsThird = [
            ['name' => 'Consulta nutricional', 'description' => 'Evaluación y plan nutricional.', 'duration' => 60, 'price' => 80000.00],
            ['name' => 'Yoga terapéutico',     'description' => 'Clase individual de yoga.',      'duration' => 60, 'price' => 45000.00],
            ['name' => 'Pilates',              'description' => 'Sesión individual de pilates.',  'duration' => 60, 'price' => 50000.00],
        ];
        $thirdServiceIds = [];
        foreach ($svcsThird as $s) {
            $thirdServiceIds[] = DB::table('services')->insertGetId(array_merge($s, [
                'image' => 'services/default.png',
                'company_id' => $thirdCompanyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // -------------------------------------------------------
        // 8. SERVICE_USER (empleado → servicios de empresa principal)
        // -------------------------------------------------------
        foreach ($mainServiceIds as $sid) {
            DB::table('service_user')->insert([
                'service_id' => $sid,
                'user_id' => $empleadoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------------------------------------
        // 9. CUSTOMERS
        // -------------------------------------------------------
        $customerMainId = DB::table('customers')->insertGetId([
            'user_id' => $clienteId,
            'company_id' => $mainCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $customerSecondId = DB::table('customers')->insertGetId([
            'user_id' => $clienteId,
            'company_id' => $secondCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $customerThirdId = DB::table('customers')->insertGetId([
            'user_id' => $clienteId,
            'company_id' => $thirdCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // -------------------------------------------------------
        // 10. HELPER para insertar citas
        // -------------------------------------------------------
        $bg = fn() => (string) Str::uuid();

        $insertAppointment = function (
            int    $customerId,
            int    $professionalId,
            int    $companyId,
            int    $serviceId,
            string $status,
            Carbon $start,
            int    $durationMinutes,
            string $notes
        ) use ($adminId, $clienteId, $bg): int {

            $end         = $start->copy()->addMinutes($durationMinutes);
            $completedAt = $status === 'completed' ? $end->copy() : null;
            $confirmedBy = in_array($status, ['confirmed', 'completed']) ? $adminId : null;
            $cancelledBy = $status === 'cancelled' ? $clienteId : null;
            $cancelReason = $status === 'cancelled' ? 'El cliente canceló la cita.' : null;

            // Solo las citas pendientes necesitan cancel_token activo
            $cancelToken        = $status === 'pending' ? uniqid() : null;
            $cancelTokenExpires = $status === 'pending' ? $start->copy()->subHours(24) : null;

            $apptId = DB::table('appointments')->insertGetId([
                'start_time'              => $start,
                'end_time'                => $end,
                'status'                  => $status,
                'notes'                   => $notes,
                'cancellation_reason'     => $cancelReason,
                'completed_at'            => $completedAt,
                'payment_expires_at'      => null,
                'cancel_token'            => $cancelToken,           // ← agregado
                'cancel_token_expires_at' => $cancelTokenExpires,   // ← agregado
                'customer_id'             => $customerId,
                'user_id'                 => $professionalId,
                'confirmed_by'            => $confirmedBy,
                'cancelled_by'            => $cancelledBy,
                'completed_by'            => $completedAt ? $adminId : null,
                'company_id'              => $companyId,
                'reminder_24h_sent'       => $status === 'completed' ? 1 : 0,
                'reminder_1h_sent'        => $status === 'completed' ? 1 : 0,
                'booking_group'           => $bg(),
                'created_at'              => $start->copy()->subDays(2),
                'updated_at'              => now(),
            ]);

          
            DB::table('appointment_service')->insert([
                'appointment_id' => $apptId,
                'service_id'     => $serviceId,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            return $apptId;
        };

        // ═══════════════════════════════════════════════════════════════
        // 11. CITAS – Estudio Glamour · EMPLEADO como profesional
        //
        //  50 citas concentradas en los últimos 30 días + próximas 2 semanas
        //  para que el dashboard muestre datos en HOY, SEMANA y MES.
        //
        //  Distribución:
        //    completed  → 30  (pasado: -30 a -1)
        //    cancelled  →  8  (pasado: -28 a -2)
        //    confirmed  →  6  (hoy y pasado reciente: -2 a 0)
        //    pending    →  6  (futuro: +1 a +14)
        // ═══════════════════════════════════════════════════════════════
        $slotsEmpleado = [
            // ── Completadas (últimos 30 días) ────────────────────
            ['days' => -30, 'hour' => '08:00', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' => -30, 'hour' => '09:00', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' => -29, 'hour' => '08:00', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' => -29, 'hour' => '10:00', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            ['days' => -28, 'hour' => '08:30', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            ['days' => -28, 'hour' => '11:00', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' => -27, 'hour' => '09:00', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' => -27, 'hour' => '13:00', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' => -26, 'hour' => '08:00', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            ['days' => -25, 'hour' => '10:00', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            ['days' => -24, 'hour' => '08:30', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' => -23, 'hour' => '09:00', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' => -22, 'hour' => '14:00', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' => -21, 'hour' => '08:00', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            ['days' => -20, 'hour' => '09:30', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            ['days' => -19, 'hour' => '11:00', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' => -18, 'hour' => '08:00', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' => -17, 'hour' => '10:00', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' => -16, 'hour' => '08:30', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            ['days' => -15, 'hour' => '09:00', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            ['days' => -14, 'hour' => '08:00', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' => -13, 'hour' => '11:00', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' => -12, 'hour' => '09:00', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' => -11, 'hour' => '14:00', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            ['days' => -10, 'hour' => '08:30', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            ['days' =>  -9, 'hour' => '09:00', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' =>  -8, 'hour' => '10:00', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' =>  -7, 'hour' => '08:00', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' =>  -6, 'hour' => '09:30', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            ['days' =>  -5, 'hour' => '11:00', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            // ── Canceladas ───────────────────────────────────────
            ['days' => -28, 'hour' => '15:00', 'dur' => 30,  'status' => 'cancelled', 'svc' => 0],
            ['days' => -24, 'hour' => '16:00', 'dur' => 45,  'status' => 'cancelled', 'svc' => 2],
            ['days' => -20, 'hour' => '14:30', 'dur' => 90,  'status' => 'cancelled', 'svc' => 1],
            ['days' => -15, 'hour' => '15:00', 'dur' => 60,  'status' => 'cancelled', 'svc' => 3],
            ['days' => -10, 'hour' => '16:00', 'dur' => 60,  'status' => 'cancelled', 'svc' => 4],
            ['days' =>  -6, 'hour' => '15:30', 'dur' => 30,  'status' => 'cancelled', 'svc' => 0],
            ['days' =>  -3, 'hour' => '16:00', 'dur' => 45,  'status' => 'cancelled', 'svc' => 2],
            ['days' =>  -2, 'hour' => '14:00', 'dur' => 60,  'status' => 'cancelled', 'svc' => 3],
            // ── Confirmadas (hoy y días recientes) ───────────────
            ['days' =>  -2, 'hour' => '08:00', 'dur' => 30,  'status' => 'confirmed', 'svc' => 0],
            ['days' =>  -1, 'hour' => '09:00', 'dur' => 60,  'status' => 'confirmed', 'svc' => 4],
            ['days' =>  -1, 'hour' => '11:00', 'dur' => 45,  'status' => 'confirmed', 'svc' => 2],
            ['days' =>   0, 'hour' => '09:00', 'dur' => 30,  'status' => 'confirmed', 'svc' => 0],
            ['days' =>   0, 'hour' => '11:00', 'dur' => 60,  'status' => 'confirmed', 'svc' => 3],
            ['days' =>   0, 'hour' => '14:00', 'dur' => 90,  'status' => 'confirmed', 'svc' => 1],
            // ── Pendientes (próximas 2 semanas) ──────────────────
            ['days' =>   1, 'hour' => '09:00', 'dur' => 30,  'status' => 'pending',   'svc' => 0],
            ['days' =>   2, 'hour' => '10:00', 'dur' => 90,  'status' => 'pending',   'svc' => 1],
            ['days' =>   5, 'hour' => '08:30', 'dur' => 45,  'status' => 'pending',   'svc' => 2],
            ['days' =>   7, 'hour' => '09:00', 'dur' => 60,  'status' => 'pending',   'svc' => 3],
            ['days' =>  10, 'hour' => '11:00', 'dur' => 60,  'status' => 'pending',   'svc' => 4],
            ['days' =>  14, 'hour' => '10:00', 'dur' => 30,  'status' => 'pending',   'svc' => 0],
        ];

        foreach ($slotsEmpleado as $slot) {
            $start = Carbon::now()->addDays($slot['days'])->setTimeFromTimeString($slot['hour']);
            $insertAppointment(
                $customerMainId,
                $empleadoId,
                $mainCompanyId,
                $mainServiceIds[$slot['svc']],
                $slot['status'],
                $start,
                $slot['dur'],
                'Cita Estudio Glamour (empleado) – seeder.'
            );
        }

        // ═══════════════════════════════════════════════════════════════
        // 12. CITAS – Estudio Glamour · ADMIN como profesional
        //
        //  40 citas en Estudio Glamour + 10 repartidas en Spa y Vital
        //
        //  Distribución en empresa principal:
        //    completed  → 25  (pasado: -30 a -1)
        //    cancelled  →  6  (pasado)
        //    confirmed  →  5  (hoy y ayer)
        //    pending    →  4  (futuro)
        // ═══════════════════════════════════════════════════════════════
        $slotsAdminMain = [
            // ── Completadas ──────────────────────────────────────
            ['days' => -30, 'hour' => '10:00', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            ['days' => -30, 'hour' => '16:00', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' => -29, 'hour' => '09:30', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' => -28, 'hour' => '14:00', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' => -27, 'hour' => '10:30', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            ['days' => -26, 'hour' => '09:00', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            ['days' => -25, 'hour' => '11:30', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' => -24, 'hour' => '10:00', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' => -23, 'hour' => '15:00', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' => -22, 'hour' => '09:00', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            ['days' => -21, 'hour' => '10:30', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            ['days' => -20, 'hour' => '08:30', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' => -19, 'hour' => '11:00', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' => -18, 'hour' => '14:30', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' => -17, 'hour' => '09:30', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            ['days' => -16, 'hour' => '10:00', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            ['days' => -15, 'hour' => '11:00', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' => -14, 'hour' => '09:00', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' => -13, 'hour' => '15:30', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' => -12, 'hour' => '10:30', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            ['days' => -11, 'hour' => '09:00', 'dur' => 60,  'status' => 'completed', 'svc' => 4],
            ['days' => -10, 'hour' => '11:30', 'dur' => 45,  'status' => 'completed', 'svc' => 2],
            ['days' =>  -9, 'hour' => '10:00', 'dur' => 30,  'status' => 'completed', 'svc' => 0],
            ['days' =>  -8, 'hour' => '14:00', 'dur' => 90,  'status' => 'completed', 'svc' => 1],
            ['days' =>  -7, 'hour' => '09:30', 'dur' => 60,  'status' => 'completed', 'svc' => 3],
            // ── Canceladas ───────────────────────────────────────
            ['days' => -27, 'hour' => '16:30', 'dur' => 30,  'status' => 'cancelled', 'svc' => 0],
            ['days' => -21, 'hour' => '15:00', 'dur' => 60,  'status' => 'cancelled', 'svc' => 4],
            ['days' => -14, 'hour' => '16:00', 'dur' => 45,  'status' => 'cancelled', 'svc' => 2],
            ['days' =>  -7, 'hour' => '15:30', 'dur' => 90,  'status' => 'cancelled', 'svc' => 1],
            ['days' =>  -4, 'hour' => '16:00', 'dur' => 60,  'status' => 'cancelled', 'svc' => 3],
            ['days' =>  -3, 'hour' => '15:00', 'dur' => 30,  'status' => 'cancelled', 'svc' => 0],
            // ── Confirmadas ──────────────────────────────────────
            ['days' =>  -1, 'hour' => '10:00', 'dur' => 45,  'status' => 'confirmed', 'svc' => 2],
            ['days' =>  -1, 'hour' => '15:00', 'dur' => 60,  'status' => 'confirmed', 'svc' => 4],
            ['days' =>   0, 'hour' => '10:00', 'dur' => 30,  'status' => 'confirmed', 'svc' => 0],
            ['days' =>   0, 'hour' => '12:00', 'dur' => 90,  'status' => 'confirmed', 'svc' => 1],
            ['days' =>   0, 'hour' => '16:00', 'dur' => 60,  'status' => 'confirmed', 'svc' => 3],
            // ── Pendientes ───────────────────────────────────────
            ['days' =>   3, 'hour' => '10:00', 'dur' => 30,  'status' => 'pending',   'svc' => 0],
            ['days' =>   6, 'hour' => '09:00', 'dur' => 60,  'status' => 'pending',   'svc' => 4],
            ['days' =>  11, 'hour' => '10:30', 'dur' => 45,  'status' => 'pending',   'svc' => 2],
            ['days' =>  13, 'hour' => '11:00', 'dur' => 90,  'status' => 'pending',   'svc' => 1],
        ];

        foreach ($slotsAdminMain as $slot) {
            $start = Carbon::now()->addDays($slot['days'])->setTimeFromTimeString($slot['hour']);
            $insertAppointment(
                $customerMainId,
                $adminId,
                $mainCompanyId,
                $mainServiceIds[$slot['svc']],
                $slot['status'],
                $start,
                $slot['dur'],
                'Cita Estudio Glamour (admin) – seeder.'
            );
        }

        // ── Admin: pocas citas en Spa Sereno ─────────────────────
        $slotsAdminSpa = [
            ['days' => -25, 'hour' => '10:00', 'status' => 'completed', 'svc' => 0],
            ['days' => -15, 'hour' => '09:00', 'status' => 'completed', 'svc' => 2],
            ['days' =>  -5, 'hour' => '11:00', 'status' => 'cancelled', 'svc' => 1],
            ['days' =>   6, 'hour' => '10:00', 'status' => 'pending',   'svc' => 0],
            ['days' =>  12, 'hour' => '09:00', 'status' => 'pending',   'svc' => 2],
        ];
        foreach ($slotsAdminSpa as $slot) {
            $start = Carbon::now()->addDays($slot['days'])->setTimeFromTimeString($slot['hour']);
            $insertAppointment(
                $customerSecondId,
                $adminId,
                $secondCompanyId,
                $secondServiceIds[$slot['svc']],
                $slot['status'],
                $start,
                60,
                'Cita Spa Sereno (admin) – seeder.'
            );
        }

        // ── Admin: pocas citas en Centro Vital ───────────────────
        $slotsAdminVital = [
            ['days' => -22, 'hour' => '09:00', 'status' => 'completed', 'svc' => 1],
            ['days' => -10, 'hour' => '14:00', 'status' => 'completed', 'svc' => 0],
            ['days' =>  -3, 'hour' => '09:00', 'status' => 'confirmed', 'svc' => 2],
            ['days' =>   9, 'hour' => '10:00', 'status' => 'pending',   'svc' => 0],
            ['days' =>  14, 'hour' => '11:00', 'status' => 'pending',   'svc' => 1],
        ];
        foreach ($slotsAdminVital as $slot) {
            $start = Carbon::now()->addDays($slot['days'])->setTimeFromTimeString($slot['hour']);
            $insertAppointment(
                $customerThirdId,
                $adminId,
                $thirdCompanyId,
                $thirdServiceIds[$slot['svc']],
                $slot['status'],
                $start,
                60,
                'Cita Centro Vital (admin) – seeder.'
            );
        }

        // -------------------------------------------------------
        // RESUMEN
        // -------------------------------------------------------
        $adminAppts    = DB::table('appointments')->where('user_id', $adminId)->count();
        $empleadoAppts = DB::table('appointments')->where('user_id', $empleadoId)->count();
        $clienteAppts  = DB::table('appointments')
            ->whereIn('customer_id', [$customerMainId, $customerSecondId, $customerThirdId])
            ->count();
        $totalAppts    = DB::table('appointments')->count();

        $this->command->info('SpecialAppointmentSeeder ejecutado correctamente.');
        $this->command->info("   → Admin     (user_id={$adminId})    | Citas como profesional : {$adminAppts}   (40 en Glamour + 5 Spa + 5 Vital)");
        $this->command->info("   → Empleado  (user_id={$empleadoId})    | Citas como profesional : {$empleadoAppts}   (50 en Glamour)");
        $this->command->info("   → Cliente   (user_id={$clienteId})    | Citas como customer    : {$clienteAppts}");
        $this->command->info("   → Total appointments : {$totalAppts}");
        $this->command->info("   → Empresa principal (Estudio Glamour) ID : {$mainCompanyId}");
    }
}
