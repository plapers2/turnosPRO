<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //* Limpiar caché de permisos de Spatie antes de crear
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        //* Permisos

        //! Servicios
        $gestionarServicios         = Permission::create(['name' => 'gestionar servicios']);
        $verServicios               = Permission::create(['name' => 'ver servicios']);

        //! Usuarios
        $gestionarUsuarios          = Permission::create(['name' => 'gestionar usuarios']);

        //! Citas
        $gestionarCitas             = Permission::create(['name' => 'gestionar citas']);
        $verHistorialCitas          = Permission::create(['name' => 'ver historial de citas']);
        $reservarCitas              = Permission::create(['name' => 'reservar citas']);

        //! Empresas
        $gestionarEmpresas          = Permission::create(['name' => 'gestionar empresas']);
        $verEmpresas                = Permission::create(['name' => 'ver empresas']);
        $verHorarios                = Permission::create(['name' => 'ver horarios']);
        $verDisponibilidades        = Permission::create(['name' => 'ver disponibilidades']);

        //! Clientes
        $gestionarClientes          = Permission::create(['name' => 'gestionar clientes']);

        //! Notificaciones
        $verHistorialNotificaciones = Permission::create(['name' => 'ver historial de notificaciones']);

        //! Reportes
        $imprimirReportes           = Permission::create(['name' => 'imprimir reportes']);

        // ! Dashboard
        $gestionarDashboard         = Permission::create(['name' => 'gestionar dashboard']);

        //! Master
        $gestionarPlataforma        = Permission::create(['name' => 'gestionar plataforma']);

        //* Roles
        $master   = Role::create(['name' => 'master']);
        $admin    = Role::create(['name' => 'admin']);
        $employee = Role::create(['name' => 'empleado']);
        $customer = Role::create(['name' => 'cliente']);

        //* Asignar

        //! Master — solo gestiona la plataforma
        $master->givePermissionTo([
            $gestionarPlataforma,
        ]);

        //! Administradores
        $admin->givePermissionTo([
            $gestionarServicios,
            $verServicios,
            $gestionarDashboard,
            $gestionarUsuarios,
            $gestionarCitas,
            $gestionarEmpresas,
            $verEmpresas,
            $verHorarios,
            $gestionarClientes,
            $verHistorialNotificaciones,
            $imprimirReportes,
            $verDisponibilidades
        ]);

        //! Empleados
        $employee->givePermissionTo([
            $gestionarCitas,
            $gestionarDashboard,
            $verHorarios,
            $verServicios,
            $verEmpresas,
            $verDisponibilidades
        ]);

        //! Clientes
        $customer->givePermissionTo([
            $verHistorialCitas,
            $reservarCitas,
        ]);
    }
}
