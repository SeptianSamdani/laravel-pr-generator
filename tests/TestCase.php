<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Outlet;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Nonaktifkan Vite saat testing (tidak perlu manifest.json)
        $this->withoutVite();

        // Reset Spatie Permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Seed roles & permissions
        $this->seedRolesAndPermissions();
    }

    protected function seedRolesAndPermissions(): void
    {
        $permissions = [
            'pr.view', 'pr.create', 'pr.edit', 'pr.delete',
            'pr.submit', 'pr.approve', 'pr.reject', 'pr.download',
            'user.view', 'user.create', 'user.edit', 'user.delete',
            'user.activate', 'user.deactivate', 'user.reset-password', 'user.assign-role',
            'role.view', 'role.create', 'role.edit', 'role.delete', 'role.assign-permission',
            'outlet.view', 'outlet.create', 'outlet.edit', 'outlet.delete',
            'report.view', 'report.export',
            'settings.view', 'settings.edit',
            'activity.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'pr.view', 'pr.create', 'pr.edit', 'pr.delete', 'pr.submit', 'pr.download',
            'user.view', 'user.create', 'user.edit', 'user.activate', 'user.deactivate',
            'user.reset-password', 'user.assign-role',
            'outlet.view', 'outlet.create', 'outlet.edit', 'outlet.delete',
            'report.view', 'report.export', 'settings.view', 'activity.view',
        ]);

        // Manager
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'pr.view', 'pr.approve', 'pr.reject', 'pr.delete', 'pr.download',
            'user.view', 'outlet.view', 'report.view', 'report.export', 'activity.view',
        ]);

        // Staff
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->givePermissionTo([
            'pr.view', 'pr.create', 'pr.edit', 'pr.delete', 'pr.submit', 'pr.download',
            'outlet.view',
        ]);

        // Viewer
        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->givePermissionTo(['pr.view', 'pr.download', 'outlet.view', 'report.view']);
    }

    // ─── Helper: Create user with role ───────────────────────────────────────

    protected function createSuperAdmin(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge(['is_active' => true], $overrides));
        $user->assignRole('super_admin');
        return $user;
    }

    protected function createAdmin(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge(['is_active' => true], $overrides));
        $user->assignRole('admin');
        return $user;
    }

    protected function createManager(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge(['is_active' => true], $overrides));
        $user->assignRole('manager');
        return $user;
    }

    protected function createStaff(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge(['is_active' => true], $overrides));
        $user->assignRole('staff');
        return $user;
    }

    protected function createViewer(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge(['is_active' => true], $overrides));
        $user->assignRole('viewer');
        return $user;
    }

    protected function createOutlet(array $overrides = []): Outlet
    {
        return Outlet::factory()->create(array_merge(['is_active' => true], $overrides));
    }
}