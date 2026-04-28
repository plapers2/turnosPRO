<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservaConcurrenciaTest extends TestCase
{
    use RefreshDatabase;

    private User $profesional;
    private User $cliente1;
    private User $cliente2;
    private Company $empresa;
    private Service $servicio;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $tipoId = \DB::table('type_companies')->insertGetId([
            'name'       => 'Tipo de prueba',
            'logo'       => 'test.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->empresa = Company::factory()->create([
            'type_company_id' => $tipoId,
        ]);

        // Profesional vinculado a la empresa
        $this->profesional = User::factory()->create();
        $this->empresa->users()->attach($this->profesional->id);

        // Horario del profesional
        \DB::table('professional_availabilities')->insert([
            ['user_id' => $this->profesional->id, 'day_of_week' => 'Monday',    'start_time' => '08:00:00', 'end_time' => '18:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $this->profesional->id, 'day_of_week' => 'Tuesday',   'start_time' => '08:00:00', 'end_time' => '18:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $this->profesional->id, 'day_of_week' => 'Wednesday', 'start_time' => '08:00:00', 'end_time' => '18:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $this->profesional->id, 'day_of_week' => 'Thursday',  'start_time' => '08:00:00', 'end_time' => '18:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $this->profesional->id, 'day_of_week' => 'Friday',    'start_time' => '08:00:00', 'end_time' => '18:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Servicio de 60 minutos
        $this->servicio = Service::factory()->create([
            'company_id' => $this->empresa->id,
            'duration'   => 60,
        ]);

        // Dos clientes distintos
        $this->cliente1 = User::factory()->create(['phone' => '3007654321']);
        $this->cliente2 = User::factory()->create(['phone' => '3009876543']);
        $rolCliente = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);

        $this->cliente1->assignRole($rolCliente);
        $this->cliente2->assignRole($rolCliente);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function solo_una_reserva_gana_cuando_dos_usuarios_reservan_el_mismo_slot()
    {
        $payload = [
            'company_id' => $this->empresa->id,
            'fecha'      => '2026-05-04', // lunes futuro
            'hora'       => '10:00',
            'user_id'    => $this->profesional->id,
            'services'   => [$this->servicio->id],
        ];

        // Simular dos requests casi simultáneos
        $respuesta1 = $this->actingAs($this->cliente1)->post(route('appointments.store'), $payload);
        $respuesta2 = $this->actingAs($this->cliente2)->post(route('appointments.store'), $payload);

        // Solo debe existir UNA cita en la BD
        $this->assertDatabaseCount('appointments', 1);

        // Una tuvo éxito y la otra recibió error
        $exitosas  = collect([$respuesta1, $respuesta2])->filter(fn($r) => $r->isRedirect(route('appointment.index')))->count();
        $fallidas  = collect([$respuesta1, $respuesta2])->filter(fn($r) => $r->isRedirect() && $r->headers->get('Location') !== route('appointment.index'))->count();

        $this->assertEquals(1, $exitosas);
        $this->assertEquals(1, $fallidas);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function dos_reservas_en_slots_distintos_ambas_se_guardan()
    {
        $this->actingAs($this->cliente1)->post(route('appointments.store'), [
            'company_id' => $this->empresa->id,
            'fecha'      => '2026-05-04',
            'hora'       => '10:00',
            'user_id'    => $this->profesional->id,
            'services'   => [$this->servicio->id],
        ]);

        $this->actingAs($this->cliente2)->post(route('appointments.store'), [
            'company_id' => $this->empresa->id,
            'fecha'      => '2026-05-04',
            'hora'       => '12:00', // slot diferente
            'user_id'    => $this->profesional->id,
            'services'   => [$this->servicio->id],
        ]);

        // Ambas deben guardarse
        $this->assertDatabaseCount('appointments', 2);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function reserva_solapada_parcialmente_tambien_es_rechazada()
    {
        // Primera cita: 10:00 - 11:00
        $this->actingAs($this->cliente1)->post(route('appointments.store'), [
            'company_id' => $this->empresa->id,
            'fecha'      => '2026-05-04',
            'hora'       => '10:00',
            'user_id'    => $this->profesional->id,
            'services'   => [$this->servicio->id],
        ]);

        // Segunda cita: 10:30 - 11:30 (solapa con la primera)
        $respuesta = $this->actingAs($this->cliente2)->post(route('appointments.store'), [
            'company_id' => $this->empresa->id,
            'fecha'      => '2026-05-04',
            'hora'       => '10:30',
            'user_id'    => $this->profesional->id,
            'services'   => [$this->servicio->id],
        ]);

        $this->assertDatabaseCount('appointments', 1);
        $respuesta->assertSessionHas('error');
    }
}
