<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Company;
use App\Models\Service;
use App\Models\TypeCompany;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. ADMIN ─────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('12345'),
            'phone'    => '3001234567',
            'image'    => null,
        ]);
        $admin->assignRole('admin');

        // ── 2. MASTER ───────────────────────────────────────────
        $master = User::create([
            'name'                => 'Master',
            'email'               => 'master@gmail.com',
            'password'            => Hash::make('12345'),
            'phone'               => '3009999999',
            'image'               => null,
            'must_change_password' => false,
        ]);
        $master->assignRole('master');

        // ── 3. EMPRESAS EXTRA (para vista master) ───────────────
        $tipoClinica  = TypeCompany::firstOrCreate(['name' => 'Clínica'], ['logo' => null]);
        $tipoBarberia = TypeCompany::firstOrCreate(['name' => 'Barbería'], ['logo' => null]);

        $empresasExtra = [
            ['name' => 'Clínica Bienestar', 'email' => 'clinica@demo.com', 'phone' => '3011111111', 'address' => 'Av. 30 #5-20, Pereira', 'type' => $tipoClinica],
            ['name' => 'Barbería El Navajero', 'email' => 'barberia@demo.com', 'phone' => '3022222222', 'address' => 'Calle 12 #8-45, Pereira', 'type' => $tipoBarberia],
        ];

        foreach ($empresasExtra as $e) {
            $empresaExtra = Company::create([
                'name'            => $e['name'],
                'email'           => $e['email'],
                'phone'           => $e['phone'],
                'address'         => $e['address'],
                'logo'            => null,
                'type_company_id' => $e['type']->id,
            ]);

            // Admin
            $adminExtra = User::create([
                'name'                => 'Admin ' . $e['name'],
                'email'               => 'admin.' . Str::slug($e['name']) . '@gmail.com',
                'password'            => Hash::make('12345'),
                'phone'               => '300' . rand(1000000, 9999999),
                'image'               => null,
                'must_change_password' => false,
            ]);
            $adminExtra->assignRole('admin');
            DB::table('company_user')->insert(['company_id' => $empresaExtra->id, 'user_id' => $adminExtra->id, 'created_at' => now(), 'updated_at' => now()]);

            // Horarios empresa
            foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $dia) {
                DB::table('opening_hours')->insert(['company_id' => $empresaExtra->id, 'day_of_week' => $dia, 'start_time' => '08:00:00', 'end_time' => '18:00:00', 'created_at' => now(), 'updated_at' => now()]);
            }

            // Servicios
            $svcExtra = collect();
            foreach ([['name' => 'Servicio A', 'duration' => 45], ['name' => 'Servicio B', 'duration' => 60]] as $sv) {
                $svcExtra->push(Service::create(['name' => $sv['name'], 'description' => $sv['name'], 'duration' => $sv['duration'], 'price' => 50000, 'image' => 'a', 'company_id' => $empresaExtra->id]));
            }

            // Profesionales
            $profsExtra = collect();
            foreach (['Profesional Uno', 'Profesional Dos'] as $pn) {
                $pu = User::create(['name' => $pn . ' ' . $e['name'], 'email' => Str::slug($pn) . '.' . Str::slug($e['name']) . '@demo.com', 'password' => Hash::make('12345'), 'phone' => '300' . rand(1000000, 9999999), 'image' => null]);
                $pu->assignRole('empleado');
                DB::table('company_user')->insert(['company_id' => $empresaExtra->id, 'user_id' => $pu->id, 'created_at' => now(), 'updated_at' => now()]);
                foreach ($svcExtra as $sv) {
                    DB::table('service_user')->insert(['service_id' => $sv->id, 'user_id' => $pu->id, 'created_at' => now(), 'updated_at' => now()]);
                }
                foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $dia) {
                    DB::table('professional_availabilities')->insert(['user_id' => $pu->id, 'day_of_week' => $dia, 'start_time' => '08:00:00', 'end_time' => '18:00:00', 'created_at' => now(), 'updated_at' => now()]);
                }
                $profsExtra->push($pu);
            }

            // Clientes
            $clientsExtra = collect();
            foreach (['Cliente Uno', 'Cliente Dos', 'Cliente Tres'] as $cn) {
                $cu = User::create(['name' => $cn, 'email' => Str::slug($cn) . '.' . Str::slug($e['name']) . '@demo.com', 'password' => Hash::make('12345'), 'phone' => '300' . rand(1000000, 9999999), 'image' => null]);
                $cu->assignRole('cliente');
                $clientsExtra->push(DB::table('customers')->insertGetId(['user_id' => $cu->id, 'company_id' => $empresaExtra->id, 'created_at' => now(), 'updated_at' => now()]));
            }

            // 200 citas
            $statusesPasadosExtra = ['completed', 'completed', 'completed', 'cancelled'];
            $horas = [9, 10, 11, 14, 15, 16];
            for ($i = 0; $i < 200; $i++) {
                $servicio = $svcExtra->random();
                $prof     = $profsExtra->random();
                $inicio  = Carbon::now()->subDays(rand(1, 60))->setTime($horas[array_rand($horas)], 0);
                $fin      = $inicio->copy()->addMinutes($servicio->duration);
                $status  = $statusesPasadosExtra[array_rand($statusesPasadosExtra)];

                $apptId = DB::table('appointments')->insertGetId([
                    'start_time'              => $inicio,
                    'end_time'                => $fin,
                    'status'                  => $status,
                    'notes'                   => null,
                    'cancel_token'            => Str::random(40),
                    'cancel_token_expires_at' => Carbon::now()->addDays(7),
                    'customer_id'             => $clientsExtra->random(),
                    'user_id'                 => $prof->id,
                    'company_id'              => $empresaExtra->id,
                    'booking_group'           => Str::uuid(),
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]);

                DB::table('appointment_service')->insert(['appointment_id' => $apptId, 'service_id' => $servicio->id, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // ── 4. EMPRESA DEMO ──────────────────────────────────────────
        $tipoSalon = TypeCompany::firstOrCreate(
            ['name' => 'Salón de Belleza'],
            ['logo' => 'https://placehold.co/600x400']
        );

        $empresa = Company::create([
            'name'           => 'Salón Pura Perfeccion',
            'email'          => 'puraperfeccion@gmail.com',
            'phone'          => '3001234567',
            'address'        => 'Calle 45 #12-30, Pereira',
            'logo'           => 'https://placehold.co/600x400',
            'type_company_id' => $tipoSalon->id,
        ]);

        DB::table('company_user')->insert([
            'company_id' => $empresa->id,
            'user_id'    => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── 5. SERVICIOS ─────────────────────────────────────────────
        $servicios = [
            ['name' => 'Corte de cabello',   'duration' => 45,  'price' => 35000],
            ['name' => 'Manicure',            'duration' => 60,  'price' => 40000],
            ['name' => 'Tinte de cabello',    'duration' => 90,  'price' => 75000],
            ['name' => 'Masaje relajante',    'duration' => 60,  'price' => 60000],
        ];

        $serviciosCreados = collect();
        foreach ($servicios as $s) {
            $serviciosCreados->push(Service::create([
                'name'        => $s['name'],
                'description' => 'Servicio profesional de ' . strtolower($s['name']),
                'duration'    => $s['duration'],
                'price'       => $s['price'],
                'image'       => '',
                'company_id'  => $empresa->id,
            ]));
        }

        $sCorte   = $serviciosCreados[0]; // 45 min
        $sManicure = $serviciosCreados[1]; // 60 min
        $sTinte   = $serviciosCreados[2]; // 90 min
        $sMasaje  = $serviciosCreados[3]; // 60 min

        // ── 6. PROFESIONALES ─────────────────────────────────────────
        $profesionales = [
            ['name' => 'Laura Gómez',   'email' => 'laura.demo@puraperfeccion.com',   'servicios' => [$sCorte, $sTinte]],
            ['name' => 'Carlos Ruiz',   'email' => 'carlos.demo@puraperfeccion.com',  'servicios' => [$sCorte, $sMasaje]],
            ['name' => 'Sofía Herrera', 'email' => 'sofia.demo@puraperfeccion.com',   'servicios' => [$sManicure, $sTinte, $sMasaje]],
        ];

        $profCreados = collect();
        foreach ($profesionales as $p) {
            $user = User::create([
                'name'     => $p['name'],
                'email'    => $p['email'],
                'password' => Hash::make('12345'),
                'phone'    => '300' . rand(1000000, 9999999),
                'image'    => null,
            ]);
            $user->assignRole('empleado');

            // Asignar a la empresa
            DB::table('company_user')->insert([
                'company_id' => $empresa->id,
                'user_id'    => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar servicios
            foreach ($p['servicios'] as $servicio) {
                DB::table('service_user')->insert([
                    'service_id' => $servicio->id,
                    'user_id'    => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Disponibilidad toda la semana
            $dias = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            foreach ($dias as $dia) {
                DB::table('professional_availabilities')->insert([
                    'user_id'     => $user->id,
                    'day_of_week' => $dia,
                    'start_time'  => '08:00:00',
                    'end_time'    => '18:00:00',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            $profCreados->push(['user' => $user, 'servicios' => $p['servicios']]);
        }

        // Horarios de empresa
        $dias = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($dias as $dia) {
            DB::table('opening_hours')->insert([
                'company_id'  => $empresa->id,
                'day_of_week' => $dia,
                'start_time'  => '08:00:00',
                'end_time'    => '18:00:00',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ── 7. CLIENTES ──────────────────────────────────────────────
        $clientesData = [
            ['name' => 'Ana Torres',    'email' => 'ana.demo@gmail.com',    'user_email' => 'ana.cliente@gmail.com'],
            ['name' => 'Pedro Salcedo', 'email' => 'pedro.demo@gmail.com',  'user_email' => 'pedro.cliente@gmail.com'],
            ['name' => 'María López',   'email' => 'maria.demo@gmail.com',  'user_email' => 'maria.cliente@gmail.com'],
        ];

        $clientesCreados = collect();
        foreach ($clientesData as $c) {
            // Usuario con rol cliente
            $userCliente = User::create([
                'name'     => $c['name'],
                'email'    => $c['user_email'],
                'password' => Hash::make('12345'),
                'phone'    => '300' . rand(1000000, 9999999),
                'image'    => null,
            ]);
            $userCliente->assignRole('cliente');

            // Registro en customers
            $customerId = DB::table('customers')->insertGetId([
                'user_id'    => $userCliente->id,
                'company_id' => $empresa->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $clientesCreados->push($customerId);

            DB::table('company_user')->insert([
                'company_id' => $empresa->id,
                'user_id'    => $userCliente->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $clientePremium = User::create([
            'name'              => 'Cliente Premium',
            'email'             => 'premium.demo@gmail.com',
            'password'          => Hash::make('12345'),
            'phone'             => '3109990000',
            'subscription_tier' => 'premium',
            'image'             => null,
        ]);
        $clientePremium->assignRole('cliente');

        // ── 8. Citas de hoy (para dashboard) ────────────────
        $horasHoy = [8, 9, 10, 11, 12, 14, 15, 16, 17];
        $citasHoy = [
            ['status' => 'completed', 'prof' => 0, 'servicio' => $sCorte,    'cliente' => 0],
            ['status' => 'completed', 'prof' => 2, 'servicio' => $sManicure, 'cliente' => 1],
            ['status' => 'completed', 'prof' => 1, 'servicio' => $sMasaje,   'cliente' => 2],
            ['status' => 'confirmed', 'prof' => 2, 'servicio' => $sTinte,    'cliente' => 0],
            ['status' => 'confirmed', 'prof' => 0, 'servicio' => $sManicure, 'cliente' => 1],
            ['status' => 'cancelled', 'prof' => 1, 'servicio' => $sCorte,    'cliente' => 2],
            ['status' => 'confirmed', 'prof' => 0, 'servicio' => $sTinte,    'cliente' => 2],
            ['status' => 'cancelled', 'prof' => 2, 'servicio' => $sMasaje,   'cliente' => 0],
            ['status' => 'cancelled', 'prof' => 1, 'servicio' => $sCorte,    'cliente' => 1],
        ];

        foreach ($citasHoy as $idx => $cita) {
            $prof     = $profCreados[$cita['prof']];
            $servicio = $cita['servicio'];
            $inicio   = Carbon::now()->setTime($horasHoy[$idx], 0);
            $fin      = $inicio->copy()->addMinutes($servicio->duration);

            $apptId = DB::table('appointments')->insertGetId([
                'start_time'              => $inicio,
                'end_time'                => $fin,
                'status'                  => $cita['status'],
                'notes'                   => null,
                'cancel_token'            => Str::random(40),
                'cancel_token_expires_at' => Carbon::now()->addDays(7),
                'customer_id'             => $clientesCreados[$cita['cliente']],
                'user_id'                 => $prof['user']->id,
                'company_id'              => $empresa->id,
                'booking_group'           => Str::uuid(),
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            DB::table('appointment_service')->insert([
                'appointment_id' => $apptId,
                'service_id'     => $servicio->id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
        // ── 9. 300 CITAS ALEATORIAS — Salón Pura Perfección ─────────
        $statusesPasados = ['completed', 'completed', 'completed', 'cancelled'];
        $serviciosPrincipales = collect([$sCorte, $sManicure, $sTinte, $sMasaje]);
        $horas = [9, 10, 11, 14, 15, 16];

        for ($i = 0; $i < 300; $i++) {
            $profRandom     = $profCreados->random();
            $servicioRandom = $serviciosPrincipales->random();
            $inicio         = Carbon::now()->subDays(rand(1, 60))->setTime($horas[array_rand($horas)], 0);
            $fin            = $inicio->copy()->addMinutes($servicioRandom->duration);
            $status         = $statusesPasados[array_rand($statusesPasados)];

            $apptId = DB::table('appointments')->insertGetId([
                'start_time'              => $inicio,
                'end_time'                => $fin,
                'status'                  => $status,
                'notes'                   => null,
                'cancel_token'            => Str::random(40),
                'cancel_token_expires_at' => Carbon::now()->addDays(7),
                'customer_id'             => $clientesCreados->random(),
                'user_id'                 => $profRandom['user']->id,
                'company_id'              => $empresa->id,
                'booking_group'           => Str::uuid(),
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            DB::table('appointment_service')->insert([
                'appointment_id' => $apptId,
                'service_id'     => $servicioRandom->id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // ── 10. 70 Citas futuras (próximos 30 días) ─────────────────────────
        $serviciosPrincipales = collect([$sCorte, $sManicure, $sTinte, $sMasaje]);

        for ($i = 0; $i < 70; $i++) {
            $profRandom     = $profCreados->random();
            $servicioRandom = $serviciosPrincipales->random();
            $inicio = Carbon::now()->addDays(rand(1, 30))->setTime($horas[array_rand($horas)], 0);
            $fin            = $inicio->copy()->addMinutes($servicioRandom->duration);
            $status         = 'confirmed';

            $apptId = DB::table('appointments')->insertGetId([
                'start_time'              => $inicio,
                'end_time'                => $fin,
                'status'                  => $status,
                'notes'                   => null,
                'cancel_token'            => Str::random(40),
                'cancel_token_expires_at' => Carbon::now()->addDays(7),
                'customer_id'             => $clientesCreados->random(),
                'user_id'                 => $profRandom['user']->id,
                'company_id'              => $empresa->id,
                'booking_group'           => Str::uuid(),
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            DB::table('appointment_service')->insert([
                'appointment_id' => $apptId,
                'service_id'     => $servicioRandom->id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // ── 11. Citas completables (confirmed + hora pasada) ───────────────
        $citasCompletables = [
            ['prof' => 0, 'servicio' => $sManicure,  'cliente' => 0, 'horas_atras' => 2],
            ['prof' => 1, 'servicio' => $sCorte,     'cliente' => 1, 'horas_atras' => 3],
            ['prof' => 2, 'servicio' => $sTinte,     'cliente' => 2, 'horas_atras' => 4],
            ['prof' => 0, 'servicio' => $sMasaje,    'cliente' => 2, 'horas_atras' => 5],
            ['prof' => 1, 'servicio' => $sManicure,  'cliente' => 0, 'horas_atras' => 6],
        ];

        foreach ($citasCompletables as $cita) {
            $prof     = $profCreados[$cita['prof']];
            $servicio = $cita['servicio'];
            $inicio   = Carbon::now()->subHours($cita['horas_atras']);
            $fin      = $inicio->copy()->addMinutes($servicio->duration);

            $apptId = DB::table('appointments')->insertGetId([
                'start_time'              => $inicio,
                'end_time'                => $fin,
                'status'                  => 'confirmed',
                'notes'                   => null,
                'cancel_token'            => Str::random(40),
                'cancel_token_expires_at' => Carbon::now()->addDays(7),
                'customer_id'             => $clientesCreados[$cita['cliente']],
                'user_id'                 => $prof['user']->id,
                'company_id'              => $empresa->id,
                'booking_group'           => Str::uuid(),
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            DB::table('appointment_service')->insert([
                'appointment_id' => $apptId,
                'service_id'     => $servicio->id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        $this->command->info(' DemoSeeder ejecutado correctamente.');
        $this->command->info(' Empresa: Salón Pura Perfeccion');
        $this->command->info(' Master:');
        $this->command->info('   master@gmail.com / 12345');
        $this->command->info(' Admin: Empresas extra (vista master)');
        $this->command->info('   admin.clinica-bienestar@gmail.com / 12345');
        $this->command->info('   admin.barberia-el-navajero@gmail.com / 12345');
        $this->command->info(' Admin:');
        $this->command->info('   admin@gmail.com / 12345');
        $this->command->info(' Profesionales demo:');
        $this->command->info('   laura.demo@puraperfeccion.com  / 12345');
        $this->command->info('   carlos.demo@puraperfeccion.com / 12345');
        $this->command->info('   sofia.demo@puraperfeccion.com  / 12345');
        $this->command->info(' Clientes demo:');
        $this->command->info('   ana.cliente@gmail.com   / 12345');
        $this->command->info('   pedro.cliente@gmail.com / 12345');
        $this->command->info('   maria.cliente@gmail.com / 12345');
        $this->command->info(' Cliente Premium (ve todas las empresas):');
        $this->command->info('   premium.demo@gmail.com  / 12345');
    }
}
