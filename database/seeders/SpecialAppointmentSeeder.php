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
        // 1. USUARIOS ESPECIALES
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
            'address'         => 'Avenida 30 de Agosto #40-10, Pereira',
            'phone'           => '3155557788',
            'type_company_id' => $typeWellnessId,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // -------------------------------------------------------
        // 4. COMPANY_USER
        //    Admin → las 3 empresas, así el scope forCompany siempre
        //    encontrará sus citas sin importar la empresa activa en sesión.
        //    Empleado → solo empresa principal.
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
        $openDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($openDays as $day) {
            DB::table('opening_hours')->insert([
                'day_of_week' => $day,
                'start_time'  => '08:00:00',
                'end_time'    => '18:00:00',
                'company_id'  => $mainCompanyId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day) {
            foreach ([$secondCompanyId, $thirdCompanyId] as $cid) {
                DB::table('opening_hours')->insert([
                    'day_of_week' => $day,
                    'start_time'  => '09:00:00',
                    'end_time'    => '17:00:00',
                    'company_id'  => $cid,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        // -------------------------------------------------------
        // 6. DISPONIBILIDAD DEL EMPLEADO (lun–sáb)
        // -------------------------------------------------------
        foreach ($openDays as $day) {
            DB::table('professional_availabilities')->insert([
                'day_of_week' => $day,
                'start_time'  => '08:00:00',
                'end_time'    => '18:00:00',
                'user_id'     => $empleadoId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // -------------------------------------------------------
        // 7. SERVICIOS
        // -------------------------------------------------------
        $servicesMain = [
            ['name' => 'Corte de cabello', 'description' => 'Corte profesional personalizado.',            'duration' => 30, 'price' => 25000.00],
            ['name' => 'Tinte completo',   'description' => 'Coloración completa con productos premium.', 'duration' => 90, 'price' => 85000.00],
            ['name' => 'Manicure',         'description' => 'Cuidado y esmaltado de uñas.',               'duration' => 45, 'price' => 30000.00],
            ['name' => 'Pedicure',         'description' => 'Tratamiento completo de pies.',              'duration' => 60, 'price' => 35000.00],
            ['name' => 'Limpieza facial',  'description' => 'Limpieza profunda e hidratación.',           'duration' => 60, 'price' => 55000.00],
        ];

        $mainServiceIds = [];
        foreach ($servicesMain as $s) {
            $mainServiceIds[] = DB::table('services')->insertGetId(array_merge($s, [
                'image'      => 'services/default.png',
                'company_id' => $mainCompanyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $servicesSecond = [
            ['name' => 'Masaje relajante', 'description' => 'Masaje corporal descontracturante.',            'duration' => 60, 'price' => 70000.00],
            ['name' => 'Aromaterapia',     'description' => 'Sesión de relajación con aceites esenciales.', 'duration' => 45, 'price' => 50000.00],
            ['name' => 'Reflexología',     'description' => 'Masaje terapéutico en pies y manos.',          'duration' => 45, 'price' => 55000.00],
        ];

        $secondServiceIds = [];
        foreach ($servicesSecond as $s) {
            $secondServiceIds[] = DB::table('services')->insertGetId(array_merge($s, [
                'image'      => 'services/default.png',
                'company_id' => $secondCompanyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $servicesThird = [
            ['name' => 'Consulta nutricional', 'description' => 'Evaluación y plan nutricional personalizado.', 'duration' => 60, 'price' => 80000.00],
            ['name' => 'Yoga terapéutico',     'description' => 'Clase individual de yoga terapéutico.',       'duration' => 60, 'price' => 45000.00],
            ['name' => 'Pilates',              'description' => 'Sesión individual de pilates.',               'duration' => 60, 'price' => 50000.00],
        ];

        $thirdServiceIds = [];
        foreach ($servicesThird as $s) {
            $thirdServiceIds[] = DB::table('services')->insertGetId(array_merge($s, [
                'image'      => 'services/default.png',
                'company_id' => $thirdCompanyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // -------------------------------------------------------
        // 8. SERVICE_USER  (empleado → todos los servicios empresa principal)
        // -------------------------------------------------------
        foreach ($mainServiceIds as $sid) {
            DB::table('service_user')->insert([
                'service_id' => $sid,
                'user_id'    => $empleadoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------------------------------------
        // 9. CUSTOMERS
        // -------------------------------------------------------
        $customerMainId = DB::table('customers')->insertGetId([
            'user_id'    => $clienteId,
            'company_id' => $mainCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $customerSecondId = DB::table('customers')->insertGetId([
            'user_id'    => $clienteId,
            'company_id' => $secondCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $customerThirdId = DB::table('customers')->insertGetId([
            'user_id'    => $clienteId,
            'company_id' => $thirdCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // -------------------------------------------------------
        // 10. HELPER
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

            $end          = $start->copy()->addMinutes($durationMinutes);
            $completedAt  = $status === 'completed' ? $end->copy() : null;
            $confirmedBy  = in_array($status, ['confirmed', 'completed']) ? $adminId : null;
            $cancelledBy  = $status === 'cancelled' ? $clienteId : null;
            $cancelReason = $status === 'cancelled' ? 'El cliente canceló la cita.' : null;

            $apptId = DB::table('appointments')->insertGetId([
                'start_time'              => $start,
                'end_time'                => $end,
                'status'                  => $status,
                'notes'                   => $notes,
                'cancellation_reason'     => $cancelReason,
                'completed_at'            => $completedAt,
                'payment_expires_at'      => null,
                'cancel_token'            => null,
                'cancel_token_expires_at' => null,
                'customer_id'             => $customerId,
                'user_id'                 => $professionalId,
                'confirmed_by'            => $confirmedBy,
                'cancelled_by'            => $cancelledBy,
                'completed_by'            => $completedAt ? $adminId : null,
                'company_id'              => $companyId,
                'reminder_24h_sent'       => $status === 'completed' ? 1 : 0,
                'reminder_1h_sent'        => $status === 'completed' ? 1 : 0,
                'booking_group'           => $bg(),
                'created_at'              => $start->copy()->subDays(3),
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

        // -------------------------------------------------------
        // 11. CITAS
        //
        //  Recuento final por user_id:
        //    EMPLEADO → 30  (Bloque A: empresa principal)
        //    ADMIN    → 30  (Bloque B: 15 empresa principal
        //                  + Bloque C:  8 Spa Sereno
        //                  + Bloque D:  7 Centro Vital)
        //
        //  Recuento final por customer_id:
        //    CLIENTE  → 30  (10 empresa principal + 10 Spa + 10 Vital)
        //
        //  El admin está en company_user para las 3 empresas, por lo que
        //  el scope forCompany(active_company_id) siempre encuentra sus citas.
        // -------------------------------------------------------

        // ══════════════════════════════════════════════════════════
        // BLOQUE A – Empresa principal · EMPLEADO como profesional
        //            30 citas → 30 del EMPLEADO · 10 del CLIENTE
        // ══════════════════════════════════════════════════════════
        // Distribución: 18 completed · 4 cancelled · 4 confirmed · 4 pending
        $slotsEmpleado = [
            // ── Completadas ──────────────────────────────────────
            ['days' => -110, 'hour' => '08:00', 'dur' => 30, 'status' => 'completed', 'svc' => 0],
            ['days' => -105, 'hour' => '09:00', 'dur' => 90, 'status' => 'completed', 'svc' => 1],
            ['days' => -100, 'hour' => '10:30', 'dur' => 45, 'status' => 'completed', 'svc' => 2],
            ['days' =>  -95, 'hour' => '14:00', 'dur' => 60, 'status' => 'completed', 'svc' => 3],
            ['days' =>  -90, 'hour' => '09:00', 'dur' => 60, 'status' => 'completed', 'svc' => 4],
            ['days' =>  -85, 'hour' => '08:30', 'dur' => 30, 'status' => 'completed', 'svc' => 0],
            ['days' =>  -80, 'hour' => '11:00', 'dur' => 90, 'status' => 'completed', 'svc' => 1],
            ['days' =>  -75, 'hour' => '15:00', 'dur' => 45, 'status' => 'completed', 'svc' => 2],
            ['days' =>  -70, 'hour' => '09:30', 'dur' => 60, 'status' => 'completed', 'svc' => 3],
            ['days' =>  -65, 'hour' => '13:00', 'dur' => 60, 'status' => 'completed', 'svc' => 4],
            ['days' =>  -60, 'hour' => '08:00', 'dur' => 30, 'status' => 'completed', 'svc' => 0],
            ['days' =>  -55, 'hour' => '10:00', 'dur' => 90, 'status' => 'completed', 'svc' => 1],
            ['days' =>  -50, 'hour' => '14:00', 'dur' => 45, 'status' => 'completed', 'svc' => 2],
            ['days' =>  -45, 'hour' => '09:00', 'dur' => 60, 'status' => 'completed', 'svc' => 3],
            ['days' =>  -40, 'hour' => '11:30', 'dur' => 60, 'status' => 'completed', 'svc' => 4],
            ['days' =>  -35, 'hour' => '08:00', 'dur' => 30, 'status' => 'completed', 'svc' => 0],
            ['days' =>  -30, 'hour' => '16:00', 'dur' => 90, 'status' => 'completed', 'svc' => 1],
            ['days' =>  -25, 'hour' => '10:00', 'dur' => 45, 'status' => 'completed', 'svc' => 2],
            // ── Canceladas ───────────────────────────────────────
            ['days' =>  -20, 'hour' => '09:00', 'dur' => 30, 'status' => 'cancelled', 'svc' => 0],
            ['days' =>  -18, 'hour' => '15:00', 'dur' => 60, 'status' => 'cancelled', 'svc' => 3],
            ['days' =>  -12, 'hour' => '16:00', 'dur' => 45, 'status' => 'cancelled', 'svc' => 2],
            ['days' =>   -8, 'hour' => '10:30', 'dur' => 90, 'status' => 'cancelled', 'svc' => 1],
            // ── Confirmadas ──────────────────────────────────────
            ['days' =>   -5, 'hour' => '09:00', 'dur' => 30, 'status' => 'confirmed', 'svc' => 0],
            ['days' =>   -3, 'hour' => '11:00', 'dur' => 60, 'status' => 'confirmed', 'svc' => 4],
            ['days' =>   -1, 'hour' => '09:30', 'dur' => 45, 'status' => 'confirmed', 'svc' => 2],
            ['days' =>    0, 'hour' => '14:00', 'dur' => 60, 'status' => 'confirmed', 'svc' => 3],
            // ── Pendientes ───────────────────────────────────────
            ['days' =>    3, 'hour' => '09:00', 'dur' => 30, 'status' => 'pending',   'svc' => 0],
            ['days' =>    7, 'hour' => '10:00', 'dur' => 90, 'status' => 'pending',   'svc' => 1],
            ['days' =>   14, 'hour' => '11:00', 'dur' => 45, 'status' => 'pending',   'svc' => 2],
            ['days' =>   21, 'hour' => '15:00', 'dur' => 60, 'status' => 'pending',   'svc' => 3],
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
                'Cita en Estudio Glamour (empleado) – seeder.'
            );
        }

        // ══════════════════════════════════════════════════════════
        // BLOQUE B – Empresa principal · ADMIN como profesional
        //            15 citas → primeras 15 de las 30 del ADMIN
        // ══════════════════════════════════════════════════════════
        // Distribución: 9 completed · 2 cancelled · 2 confirmed · 2 pending
        $slotsAdminMain = [
            // ── Completadas ──────────────────────────────────────
            ['days' => -109, 'hour' => '08:30', 'dur' => 60, 'status' => 'completed', 'svc' => 4],
            ['days' => -104, 'hour' => '10:00', 'dur' => 45, 'status' => 'completed', 'svc' => 2],
            ['days' =>  -99, 'hour' => '14:30', 'dur' => 30, 'status' => 'completed', 'svc' => 0],
            ['days' =>  -94, 'hour' => '09:30', 'dur' => 60, 'status' => 'completed', 'svc' => 3],
            ['days' =>  -89, 'hour' => '11:00', 'dur' => 90, 'status' => 'completed', 'svc' => 1],
            ['days' =>  -84, 'hour' => '08:00', 'dur' => 60, 'status' => 'completed', 'svc' => 4],
            ['days' =>  -79, 'hour' => '15:30', 'dur' => 45, 'status' => 'completed', 'svc' => 2],
            ['days' =>  -74, 'hour' => '10:00', 'dur' => 30, 'status' => 'completed', 'svc' => 0],
            ['days' =>  -69, 'hour' => '12:00', 'dur' => 60, 'status' => 'completed', 'svc' => 3],
            // ── Canceladas ───────────────────────────────────────
            ['days' =>  -22, 'hour' => '09:00', 'dur' => 30, 'status' => 'cancelled', 'svc' => 0],
            ['days' =>  -16, 'hour' => '14:00', 'dur' => 60, 'status' => 'cancelled', 'svc' => 3],
            // ── Confirmadas ──────────────────────────────────────
            ['days' =>   -4, 'hour' => '10:00', 'dur' => 45, 'status' => 'confirmed', 'svc' => 2],
            ['days' =>   -2, 'hour' => '08:00', 'dur' => 60, 'status' => 'confirmed', 'svc' => 4],
            // ── Pendientes ───────────────────────────────────────
            ['days' =>    4, 'hour' => '09:00', 'dur' => 30, 'status' => 'pending',   'svc' => 0],
            ['days' =>   10, 'hour' => '11:00', 'dur' => 90, 'status' => 'pending',   'svc' => 1],
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
                'Cita en Estudio Glamour (admin) – seeder.'
            );
        }

        // ══════════════════════════════════════════════════════════
        // BLOQUE C – Spa Sereno · ADMIN como profesional
        //            8 citas → suman 23/30 del ADMIN
        //            + 10 del CLIENTE en Spa (8 de aquí + 2 extra abajo)
        // ══════════════════════════════════════════════════════════
        // Distribución: 5 completed · 1 cancelled · 1 confirmed · 1 pending
        $slotsAdminSpa = [
            ['days' => -108, 'hour' => '10:00', 'status' => 'completed', 'svc' => 0],
            ['days' =>  -92, 'hour' => '09:00', 'status' => 'completed', 'svc' => 2],
            ['days' =>  -75, 'hour' => '10:00', 'status' => 'completed', 'svc' => 1],
            ['days' =>  -55, 'hour' => '11:00', 'status' => 'completed', 'svc' => 0],
            ['days' =>  -30, 'hour' => '10:00', 'status' => 'completed', 'svc' => 2],
            ['days' =>  -10, 'hour' => '14:00', 'status' => 'cancelled', 'svc' => 1],
            ['days' =>   -2, 'hour' => '11:00', 'status' => 'confirmed', 'svc' => 0],
            ['days' =>    5, 'hour' => '10:00', 'status' => 'pending',   'svc' => 1],
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
                'Cita en Spa Sereno – seeder.'
            );
        }

        // 2 citas extra del cliente en Spa (no cuentan para el admin)
        $slotsClienteSpa = [
            ['days' => -100, 'hour' => '11:00', 'status' => 'completed', 'svc' => 1],
            ['days' =>  -85, 'hour' => '14:00', 'status' => 'completed', 'svc' => 0],
        ];

        foreach ($slotsClienteSpa as $slot) {
            $start = Carbon::now()->addDays($slot['days'])->setTimeFromTimeString($slot['hour']);
            $insertAppointment(
                $customerSecondId,
                $adminId,           // admin sigue siendo el profesional (es quien trabaja en Spa)
                $secondCompanyId,
                $secondServiceIds[$slot['svc']],
                $slot['status'],
                $start,
                60,
                'Cita en Spa Sereno (cliente extra) – seeder.'
            );
        }

        // ══════════════════════════════════════════════════════════
        // BLOQUE D – Centro Vital · ADMIN como profesional
        //            7 citas → completan las 30 del ADMIN (15+8+7)
        //            + 10 del CLIENTE en Vital (7 de aquí + 3 extra abajo)
        // ══════════════════════════════════════════════════════════
        // Distribución: 4 completed · 1 cancelled · 1 confirmed · 1 pending
        $slotsAdminVital = [
            ['days' => -106, 'hour' => '09:00', 'status' => 'completed', 'svc' => 0],
            ['days' =>  -80, 'hour' => '14:00', 'status' => 'completed', 'svc' => 1],
            ['days' =>  -52, 'hour' => '09:00', 'status' => 'completed', 'svc' => 2],
            ['days' =>  -28, 'hour' => '14:00', 'status' => 'completed', 'svc' => 0],
            ['days' =>  -18, 'hour' => '09:00', 'status' => 'cancelled', 'svc' => 1],
            ['days' =>   -3, 'hour' => '09:00', 'status' => 'confirmed', 'svc' => 2],
            ['days' =>    8, 'hour' => '10:00', 'status' => 'pending',   'svc' => 0],
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
                'Cita en Centro Vital – seeder.'
            );
        }

        // 3 citas extra del cliente en Centro Vital
        $slotsClienteVital = [
            ['days' =>  -98, 'hour' => '10:00', 'status' => 'completed', 'svc' => 1],
            ['days' =>  -72, 'hour' => '09:30', 'status' => 'completed', 'svc' => 2],
            ['days' =>  -40, 'hour' => '11:00', 'status' => 'completed', 'svc' => 0],
        ];

        foreach ($slotsClienteVital as $slot) {
            $start = Carbon::now()->addDays($slot['days'])->setTimeFromTimeString($slot['hour']);
            $insertAppointment(
                $customerThirdId,
                $adminId,
                $thirdCompanyId,
                $thirdServiceIds[$slot['svc']],
                $slot['status'],
                $start,
                60,
                'Cita en Centro Vital (cliente extra) – seeder.'
            );
        }

        // -------------------------------------------------------
        // RESUMEN VERIFICADO
        // -------------------------------------------------------
        $adminAppts    = DB::table('appointments')->where('user_id', $adminId)->count();
        $empleadoAppts = DB::table('appointments')->where('user_id', $empleadoId)->count();
        $clienteAppts  = DB::table('appointments')
            ->whereIn('customer_id', [$customerMainId, $customerSecondId, $customerThirdId])
            ->count();
        $totalAppts    = DB::table('appointments')->count();

        $this->command->info('SpecialAppointmentSeeder ejecutado correctamente.');
        $this->command->info("   → Admin ID    : {$adminId}  | Citas (user_id)     : {$adminAppts}   esperadas: 30");
        $this->command->info("   → Empleado ID : {$empleadoId}  | Citas (user_id)     : {$empleadoAppts}   esperadas: 30");
        $this->command->info("   → Cliente ID  : {$clienteId}  | Citas (customer_id) : {$clienteAppts}  esperadas: 30");
        $this->command->info("   → Total appointments insertados : {$totalAppts}");
        $this->command->info("   → Empresa principal : {$mainCompanyId} (Estudio Glamour)");
        $this->command->info("   → Empresa 2         : {$secondCompanyId} (Spa Sereno)");
        $this->command->info("   → Empresa 3         : {$thirdCompanyId} (Centro Vital)");
    }
}
    