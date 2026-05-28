<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $resources = [
            'offices', 'users', 'clients', 'cases', 'hearings',
            'documents', 'expenses', 'payments', 'invoices',
            'payment_gateways', 'enforcement_files', 'powers_of_attorney',
            'legislation', 'case_laws', 'support_tickets', 'reports', 'settings',
        ];

        $actions = ['view', 'create', 'edit', 'delete', 'export', 'manage'];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}_{$resource}"]);
            }
        }

        $superAdmin  = Role::firstOrCreate(['name' => 'super_admin']);
        $officeAdmin = Role::firstOrCreate(['name' => 'office_admin']);
        $lawyer      = Role::firstOrCreate(['name' => 'lawyer']);
        $assistant   = Role::firstOrCreate(['name' => 'assistant']);
        Role::firstOrCreate(['name' => 'client']);

        $superAdmin->givePermissionTo(Permission::all());

        $officeAdmin->givePermissionTo(
            Permission::where('name', 'not like', '%offices%')
                      ->orWhere('name', 'view_offices')
                      ->get()
        );

        $lawyer->givePermissionTo([
            'view_cases', 'create_cases', 'edit_cases',
            'view_hearings', 'create_hearings', 'edit_hearings',
            'view_documents', 'create_documents', 'edit_documents',
            'view_clients',
        ]);

        $assistant->givePermissionTo([
            'view_cases', 'create_cases',
            'view_hearings', 'create_hearings',
            'view_documents', 'create_documents',
        ]);

        // client role intentionally has minimal permissions
        // portal access is handled by middleware, not Spatie permissions
    }
}
