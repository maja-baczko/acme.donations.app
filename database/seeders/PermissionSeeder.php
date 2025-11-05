<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Campaign permissions
            'view campaigns',
            'create campaigns',
            'edit campaigns',
            'delete campaigns',

            // Donation permissions
            'view donations',
            'create donations',
            'edit donations',
            'delete donations',

            // System settings permissions
            'view system settings',
            'edit system settings',

            // Audit log permissions
            'view audit logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->syncPermissions([
            'view users',
            'view campaigns',
            'create campaigns',
            'edit campaigns',
            'view donations',
            'create donations',
            'edit donations',
            'view audit logs',
        ]);

        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeeRole->syncPermissions([
            'view campaigns',
            'view donations',
            'create donations',
        ]);
    }
}
