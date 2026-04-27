<?php

namespace Database\Seeders;

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
        // Permisos
        Permission::create(['name' => 'gestionar servicios']);
        Permission::create(['name' => 'gestionar usuarios']);
        Permission::create(['name' => 'gestionar citas']);
        Permission::create(['name' => 'gestionar empresas']);


        // Roles
        $admin = Role::create(['name' => 'admin']);
        $employee = Role::create(['name' => 'empleado']);
        $costumer = Role::create(['name' => 'cliente']);

        // Asignar permisos
        $admin->givePermissionTo(Permission::all());

        $employee->givePermissionTo([
            'gestionar citas',
        ]);

        $costumer->givePermissionTo([
            'gestionar servicios'
        ]);
    }
}
