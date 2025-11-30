<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // PR Permissions
            'pr.view',
            'pr.create',
            'pr.edit',
            'pr.delete',
            'pr.submit',
            'pr.approve',
            'pr.reject',
            'pr.download',
            
            // User Management Permissions
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.activate',
            'user.deactivate',
            'user.reset-password',
            'user.assign-role',
            
            // Role & Permission Management
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'role.assign-permission',
            
            // Outlet Permissions
            'outlet.view',
            'outlet.create',
            'outlet.edit',
            'outlet.delete',
            
            // Report Permissions
            'report.view',
            'report.export',
            
            // System Permissions
            'settings.view',
            'settings.edit',
            'activity.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // 1. Super Admin Role
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Admin Role
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'pr.view', 'pr.create', 'pr.edit', 'pr.delete', 'pr.submit', 'pr.download',
            'user.view', 'user.create', 'user.edit', 'user.activate', 'user.deactivate', 'user.reset-password', 'user.assign-role',
            'outlet.view', 'outlet.create', 'outlet.edit', 'outlet.delete',
            'report.view', 'report.export',
            'settings.view', 'activity.view',
        ]);

        // 3. Manager Role
        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'pr.view', 'pr.approve', 'pr.reject', 'pr.download',
            'user.view',
            'outlet.view',
            'report.view', 'report.export',
            'activity.view',
        ]);

        // 4. Staff Role
        $staff = Role::create(['name' => 'staff']);
        $staff->givePermissionTo([
            'pr.view', 'pr.create', 'pr.edit', 'pr.delete', 'pr.submit', 'pr.download',
            'outlet.view',
        ]);

        // 5. Viewer Role (Read-only)
        $viewer = Role::create(['name' => 'viewer']);
        $viewer->givePermissionTo([
            'pr.view', 'pr.download',
            'outlet.view',
            'report.view',
        ]);

        // Create Users and Assign Roles

        // Super Admin User
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@company.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $superAdminUser->assignRole('super_admin');

        // Admin User
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@company.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        // Manager User
        $managerUser = User::create([
            'name' => 'Manager User',
            'email' => 'manager@company.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $managerUser->assignRole('manager');

        // Staff User 1
        $staffUser1 = User::create([
            'name' => 'Staff Marketing 1',
            'email' => 'staff1@company.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $staffUser1->assignRole('staff');

        // Staff User 2
        $staffUser2 = User::create([
            'name' => 'Staff Marketing 2',
            'email' => 'staff2@company.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $staffUser2->assignRole('staff');

        // Viewer User
        $viewerUser = User::create([
            'name' => 'Finance Viewer',
            'email' => 'viewer@company.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $viewerUser->assignRole('viewer');

        $this->command->info('Roles and Permissions seeded successfully!');
        $this->command->info('Login Credentials:');
        $this->command->info('Super Admin: superadmin@company.com / password');
        $this->command->info('Admin: admin@company.com / password');
        $this->command->info('Manager: manager@company.com / password');
        $this->command->info('Staff 1: staff1@company.com / password');
        $this->command->info('Staff 2: staff2@company.com / password');
        $this->command->info('Viewer: viewer@company.com / password');
    }
}