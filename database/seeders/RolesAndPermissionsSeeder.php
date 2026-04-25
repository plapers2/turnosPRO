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


        // Roles
        $admin = Role::create(['name' => 'admin']);
        $guardia = Role::create(['name' => 'empleado']);

        // Asignar permisos
        $admin->givePermissionTo(Permission::all());

        $guardia->givePermissionTo([
            'gestionar citas',
        ]);
    }
}
