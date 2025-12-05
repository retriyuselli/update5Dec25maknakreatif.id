<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SopPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // SOP Permissions
        $sopPermissions = [
            // SOP Category permissions
            'view_any_sop_category',
            'view_sop_category',
            'create_sop_category',
            'update_sop_category',
            'delete_sop_category',
            'delete_any_sop_category',
            'force_delete_sop_category',
            'force_delete_any_sop_category',
            'restore_sop_category',
            'restore_any_sop_category',

            // SOP permissions
            'view_any_sop',
            'view_sop',
            'create_sop',
            'update_sop',
            'delete_sop',
            'delete_any_sop',
            'force_delete_sop',
            'force_delete_any_sop',
            'restore_sop',
            'restore_any_sop',

            // SOP special permissions
            'duplicate_sop',
            'export_sop',
            'manage_sop_revisions',

            // Public SOP view permissions (for regular users)
            'view_public_sop',
            'search_sop',
        ];

        foreach ($sopPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $superAdmin = Role::where('name', 'super_admin')->first();
        $admin = Role::where('name', 'admin')->first();
        $accountManager = Role::where('name', 'Account Manager')->first();
        $employee = Role::where('name', 'employee')->first();

        if ($superAdmin) {
            $superAdmin->givePermissionTo($sopPermissions);
        }

        if ($admin) {
            $admin->givePermissionTo([
                'view_any_sop_category',
                'view_sop_category',
                'create_sop_category',
                'update_sop_category',
                'delete_sop_category',
                'view_any_sop',
                'view_sop',
                'create_sop',
                'update_sop',
                'delete_sop',
                'duplicate_sop',
                'export_sop',
                'manage_sop_revisions',
                'view_public_sop',
                'search_sop',
            ]);
        }

        if ($accountManager) {
            $accountManager->givePermissionTo([
                'view_any_sop',
                'view_sop',
                'create_sop',
                'update_sop',
                'view_public_sop',
                'search_sop',
            ]);
        }

        if ($employee) {
            $employee->givePermissionTo([
                'view_public_sop',
                'search_sop',
            ]);
        }

        $this->command->info('SOP permissions created and assigned successfully!');
    }
}
