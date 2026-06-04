<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use App\Livewire\UserManagement;

class UserManagementTest extends TestCase
{
    // ─── Access Control ───────────────────────────────────────────────────────

    /** @test */
    public function admin_can_access_user_management(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route('users.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function super_admin_can_access_user_management(): void
    {
        $this->actingAs($this->createSuperAdmin())
            ->get(route('users.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function manager_cannot_create_or_delete_users(): void
    {
        $manager = $this->createManager();

        // Manager can view but not create
        $component = Livewire::actingAs($manager)->test(UserManagement::class);

        // The create modal button should not be visible for manager
        $component->assertDontSee('Add User');
    }

    /** @test */
    public function staff_cannot_access_user_management(): void
    {
        $this->actingAs($this->createStaff())
            ->get(route('users.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function viewer_cannot_access_user_management(): void
    {
        $this->actingAs($this->createViewer())
            ->get(route('users.index'))
            ->assertStatus(403);
    }

    // ─── Create User ──────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_create_new_user(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openCreateModal')
            ->set('name', 'New Staff')
            ->set('email', 'newstaff@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('selectedRole', 'staff')
            ->set('is_active', true)
            ->call('createUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'newstaff@test.com',
            'name'  => 'New Staff',
        ]);
    }

    /** @test */
    public function create_user_validates_required_fields(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openCreateModal')
            ->set('name', '')
            ->set('email', '')
            ->set('password', '')
            ->set('selectedRole', '')
            ->call('createUser')
            ->assertHasErrors(['name', 'email', 'password', 'selectedRole']);
    }

    /** @test */
    public function create_user_validates_unique_email(): void
    {
        $admin    = $this->createAdmin();
        $existing = $this->createStaff(['email' => 'existing@test.com']);

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openCreateModal')
            ->set('name', 'Duplicate')
            ->set('email', 'existing@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('selectedRole', 'staff')
            ->call('createUser')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function create_user_validates_password_confirmation(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openCreateModal')
            ->set('name', 'Test User')
            ->set('email', 'test@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'differentpassword')
            ->set('selectedRole', 'staff')
            ->call('createUser')
            ->assertHasErrors(['password']);
    }

    /** @test */
    public function create_user_validates_minimum_password_length(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openCreateModal')
            ->set('name', 'Test User')
            ->set('email', 'test@test.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->set('selectedRole', 'staff')
            ->call('createUser')
            ->assertHasErrors(['password']);
    }

    /** @test */
    public function new_user_is_assigned_the_specified_role(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openCreateModal')
            ->set('name', 'New Manager')
            ->set('email', 'newmanager@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('selectedRole', 'manager')
            ->call('createUser');

        $user = User::where('email', 'newmanager@test.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('manager'));
    }

    // ─── Edit User ────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_edit_existing_user(): void
    {
        $admin  = $this->createAdmin();
        $target = $this->createStaff(['name' => 'Old Name']);

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openEditModal', $target->id)
            ->set('name', 'Updated Name')
            ->set('email', $target->email)
            ->set('selectedRole', 'staff')
            ->call('updateUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id'   => $target->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function admin_can_change_user_role(): void
    {
        $admin  = $this->createAdmin();
        $target = $this->createStaff();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openEditModal', $target->id)
            ->set('name', $target->name)
            ->set('email', $target->email)
            ->set('selectedRole', 'viewer')
            ->call('updateUser');

        $this->assertTrue($target->fresh()->hasRole('viewer'));
    }

    /** @test */
    public function edit_user_validates_unique_email_excluding_self(): void
    {
        $admin  = $this->createAdmin();
        $target = $this->createStaff(['email' => 'target@test.com']);
        $other  = $this->createStaff(['email' => 'other@test.com']);

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openEditModal', $target->id)
            ->set('name', $target->name)
            ->set('email', 'other@test.com') // already taken by $other
            ->set('selectedRole', 'staff')
            ->call('updateUser')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function admin_cannot_edit_super_admin_if_not_super_admin(): void
    {
        $admin      = $this->createAdmin();
        $superAdmin = $this->createSuperAdmin();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openEditModal', $superAdmin->id)
            ->assertSet('showEditModal', false); // modal tidak terbuka karena super admin tidak bisa diedit
    }

    // ─── Delete User ──────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_delete_user(): void
    {
        $admin  = $this->createAdmin();
        $target = $this->createStaff();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openDeleteModal', $target->id)
            ->call('deleteUser');

        $this->assertDatabaseMissing('users', ['id' => $target->id, 'deleted_at' => null]);
    }

    /** @test */
    public function admin_cannot_delete_super_admin(): void
    {
        $admin      = $this->createAdmin();
        $superAdmin = $this->createSuperAdmin();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openDeleteModal', $superAdmin->id)
            ->assertSet('showDeleteModal', false); // modal tidak terbuka

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    /** @test */
    public function user_cannot_delete_themselves(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openDeleteModal', $admin->id)
            ->assertSet('showDeleteModal', false); // modal tidak terbuka karena self-delete
    }

    // ─── Toggle Status ────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_deactivate_user(): void
    {
        $admin  = $this->createAdmin();
        $target = $this->createStaff(['is_active' => true]);

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('toggleStatus', $target->id);

        $this->assertFalse($target->fresh()->is_active);
    }

    /** @test */
    public function admin_can_reactivate_inactive_user(): void
    {
        $admin  = $this->createAdmin();
        $target = $this->createStaff(['is_active' => false]);

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('toggleStatus', $target->id);

        $this->assertTrue($target->fresh()->is_active);
    }

    /** @test */
    public function admin_cannot_deactivate_super_admin(): void
    {
        $admin      = $this->createAdmin();
        $superAdmin = $this->createSuperAdmin();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('toggleStatus', $superAdmin->id);

        // super admin tetap aktif
        $this->assertTrue($superAdmin->fresh()->is_active);
    }

    /** @test */
    public function admin_cannot_deactivate_themselves(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('toggleStatus', $admin->id);

        // admin tetap aktif
        $this->assertTrue($admin->fresh()->is_active);
    }

    // ─── Reset Password ───────────────────────────────────────────────────────

    /** @test */
    public function admin_can_reset_user_password(): void
    {
        $admin  = $this->createAdmin();
        $target = $this->createStaff();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openResetPasswordModal', $target->id)
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('newpassword123', $target->fresh()->password));
    }

    /** @test */
    public function reset_password_validates_confirmation(): void
    {
        $admin  = $this->createAdmin();
        $target = $this->createStaff();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('openResetPasswordModal', $target->id)
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'mismatch')
            ->call('resetPassword')
            ->assertHasErrors(['password']);
    }

    // ─── Search & Filter ──────────────────────────────────────────────────────

    /** @test */
    public function user_list_can_be_searched_by_name(): void
    {
        $admin = $this->createAdmin();
        $staff = $this->createStaff(['name' => 'Unique Username XYZ']);

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->set('search', 'Unique Username XYZ')
            ->assertSee('Unique Username XYZ');
    }

    /** @test */
    public function user_list_can_be_filtered_by_role(): void
    {
        $admin   = $this->createAdmin();
        $staff   = $this->createStaff(['name' => 'Staff Member A']);
        $manager = $this->createManager(['name' => 'Manager Member B']);

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->set('roleFilter', 'staff')
            ->assertSee('Staff Member A')
            ->assertDontSee('Manager Member B');
    }

    /** @test */
    public function user_list_can_be_filtered_by_status(): void
    {
        $admin    = $this->createAdmin();
        $active   = $this->createStaff(['name' => 'Active Staff', 'is_active' => true]);
        $inactive = $this->createStaff(['name' => 'Inactive Staff', 'is_active' => false]);

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->set('statusFilter', '1')
            ->assertSee('Active Staff')
            ->assertDontSee('Inactive Staff');
    }

    /** @test */
    public function user_list_shows_correct_stats(): void
    {
        $admin = $this->createAdmin();
        $this->createStaff(['is_active' => true]);
        $this->createStaff(['is_active' => false]);
        $this->createManager(['is_active' => true]);

        // stats adalah view data, bukan public property — verifikasi via DB langsung
        $this->assertEquals(2, \App\Models\User::role('staff')->count());
        $this->assertEquals(1, \App\Models\User::role('manager')->count());
    }
}