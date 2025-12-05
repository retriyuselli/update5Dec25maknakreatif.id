<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions (add more as needed)
        $permissions = [
            'view_prospects',
            'create_prospects',
            'edit_prospects',
            'delete_prospects',
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_reports',
            'manage_users',
            'manage_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $accountManager = Role::firstOrCreate(['name' => 'Account Manager']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $employee = Role::firstOrCreate(['name' => 'employee']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo(Permission::all());

        $accountManager->givePermissionTo([
            'view_prospects',
            'create_prospects',
            'edit_prospects',
            'view_orders',
            'create_orders',
            'edit_orders',
            'view_products',
            'view_reports',
        ]);

        $admin->givePermissionTo([
            'view_prospects',
            'create_prospects',
            'edit_prospects',
            'delete_prospects',
            'view_orders',
            'create_orders',
            'edit_orders',
            'view_products',
            'create_products',
            'edit_products',
            'view_reports',
        ]);

        $employee->givePermissionTo([
            'view_prospects',
            'view_orders',
            'view_products',
        ]);

        $this->command->info('✅ Roles and permissions created successfully!');
        $this->command->newLine();
    }
}
