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
        // ── 0. ADMIN ─────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('12345'),
            'phone'    => '3001234567',
            'image'    => 'https://placehold.co/600x400',
        ]);
        $admin->assignRole('admin');
        
        // ── 1. EMPRESA DEMO ──────────────────────────────────────────
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

        // ── 2. SERVICIOS ─────────────────────────────────────────────
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
                'image'       => 'https://placehold.co/300x200',
                'company_id'  => $empresa->id,
            ]));
        }

        $sCorte   = $serviciosCreados[0]; // 45 min
        $sManicure = $serviciosCreados[1]; // 60 min
        $sTinte   = $serviciosCreados[2]; // 90 min
        $sMasaje  = $serviciosCreados[3]; // 60 min

        // ── 3. PROFESIONALES ─────────────────────────────────────────
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
                'image'    => 'https://placehold.co/600x400',
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

        // ── 4. CLIENTES ──────────────────────────────────────────────
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
                'image'    => 'https://placehold.co/600x400',
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
        }

        // ── 5. HISTORIAL DE CITAS (últimas 4 semanas) ────────────────
        $citas = [
            // Semana -4
            ['dias' => -28, 'hora' => 9,  'status' => 'completed', 'prof' => 0, 'servicio' => $sCorte,    'cliente' => 0],
            ['dias' => -27, 'hora' => 10, 'status' => 'completed', 'prof' => 2, 'servicio' => $sManicure, 'cliente' => 1],
            ['dias' => -26, 'hora' => 14, 'status' => 'cancelled', 'prof' => 1, 'servicio' => $sMasaje,   'cliente' => 2],
            ['dias' => -25, 'hora' => 11, 'status' => 'completed', 'prof' => 0, 'servicio' => $sTinte,    'cliente' => 0],

            // Semana -3
            ['dias' => -21, 'hora' => 9,  'status' => 'completed', 'prof' => 1, 'servicio' => $sMasaje,   'cliente' => 1],
            ['dias' => -20, 'hora' => 10, 'status' => 'completed', 'prof' => 2, 'servicio' => $sTinte,    'cliente' => 2],
            ['dias' => -19, 'hora' => 15, 'status' => 'cancelled', 'prof' => 0, 'servicio' => $sCorte,    'cliente' => 0],
            ['dias' => -18, 'hora' => 11, 'status' => 'completed', 'prof' => 2, 'servicio' => $sManicure, 'cliente' => 1],

            // Semana -2
            ['dias' => -14, 'hora' => 9,  'status' => 'completed', 'prof' => 0, 'servicio' => $sCorte,    'cliente' => 2],
            ['dias' => -13, 'hora' => 10, 'status' => 'completed', 'prof' => 1, 'servicio' => $sMasaje,   'cliente' => 0],
            ['dias' => -12, 'hora' => 14, 'status' => 'cancelled', 'prof' => 2, 'servicio' => $sTinte,    'cliente' => 1],
            ['dias' => -11, 'hora' => 11, 'status' => 'completed', 'prof' => 0, 'servicio' => $sTinte,    'cliente' => 2],

            // Semana -1
            ['dias' => -7,  'hora' => 9,  'status' => 'completed',  'prof' => 2, 'servicio' => $sManicure, 'cliente' => 0],
            ['dias' => -6,  'hora' => 10, 'status' => 'confirmed',  'prof' => 1, 'servicio' => $sMasaje,   'cliente' => 1],
            ['dias' => -5,  'hora' => 14, 'status' => 'completed',  'prof' => 0, 'servicio' => $sCorte,    'cliente' => 2],

            // Esta semana / próximas
            ['dias' => 1,   'hora' => 9,  'status' => 'pending',   'prof' => 2, 'servicio' => $sTinte,    'cliente' => 0],
            ['dias' => 2,   'hora' => 10, 'status' => 'pending',   'prof' => 0, 'servicio' => $sCorte,    'cliente' => 1],
            ['dias' => 3,   'hora' => 11, 'status' => 'confirmed', 'prof' => 1, 'servicio' => $sMasaje,   'cliente' => 2],
            ['dias' => 5,   'hora' => 14, 'status' => 'pending',   'prof' => 2, 'servicio' => $sManicure, 'cliente' => 0],
        ];

        foreach ($citas as $cita) {
            $prof     = $profCreados[$cita['prof']];
            $servicio = $cita['servicio'];
            $inicio   = Carbon::now()->addDays($cita['dias'])->setTime($cita['hora'], 0);
            $fin      = $inicio->copy()->addMinutes($servicio->duration);

            $appointmentId = DB::table('appointments')->insertGetId([
                'start_time'             => $inicio,
                'end_time'               => $fin,
                'status'                 => $cita['status'],
                'notes'                  => null,
                'cancel_token'           => Str::random(40),
                'cancel_token_expires_at' => Carbon::now()->addDays(7),
                'customer_id'            => $clientesCreados[$cita['cliente']],
                'user_id'                => $prof['user']->id,
                'company_id'             => $empresa->id,
                'booking_group'          => Str::uuid(),
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);

            DB::table('appointment_service')->insert([
                'appointment_id' => $appointmentId,
                'service_id'     => $servicio->id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        $this->command->info(' DemoSeeder ejecutado correctamente.');
        $this->command->info(' Empresa: Salón Pura Perfeccion');
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
    }
}
