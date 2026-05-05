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
        //* Permisos

        //! Servicios
        Permission::create(['name' => 'gestionar servicios']);
        Permission::create(['name' => 'ver servicios']);

        //! Usuarios
        Permission::create(['name' => 'gestionar usuarios']);

        //! Citas
        Permission::create(['name' => 'gestionar citas']);
        Permission::create(['name' => 'ver historial de citas']);
        Permission::create(['name' => 'reservar citas']);

        //! Empresas
        Permission::create(['name' => 'gestionar empresas']);
        Permission::create(['name' => 'ver horarios']);

        //! Clientes
        Permission::create(['name' => 'gestionar clientes']);

        //! Notificaciones
        Permission::create(['name' => 'ver historial de notificaciones']);

        //! Reportes
        Permission::create(['name' => 'imprimir reportes']);

        // ! Dashboard
        Permission::create(['name' => 'gestionar dashboard']);

        //* Roles
        $admin = Role::create(['name' => 'admin']);
        $employee = Role::create(['name' => 'empleado']);
        $customer = Role::create(['name' => 'cliente']);

        //* Asignar

        //! Administradores
        $admin->syncPermissions(
            Permission::all()
        );
        //? Remover permisos del admin
        $admin->revokePermissionTo('ver historial de citas');
        $admin->revokePermissionTo('reservar citas');

        //! Empleados
        $employee->givePermissionTo([
            'gestionar citas',
            'gestionar dashboard',
            'ver horarios',
            'ver servicios'
        ]);

        //! Clientes
        $customer->givePermissionTo([
            'ver historial de citas',
            'reservar citas',
            'gestionar dashboard'
        ]);
    }
}
