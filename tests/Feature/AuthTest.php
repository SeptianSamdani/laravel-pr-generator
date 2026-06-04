<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    // ─── Login Page ──────────────────────────────────────────────────────────

    /** @test */
    public function login_page_is_accessible_for_guests(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    /** @test */
    public function authenticated_users_are_redirected_from_login(): void
    {
        $user = $this->createStaff();
        $this->actingAs($user)->get('/login')->assertRedirect();
    }

    // ─── Login Success ────────────────────────────────────────────────────────

    /** @test */
    public function staff_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email'     => 'staff@test.com',
            'password'  => Hash::make('password123'),
            'is_active' => true,
        ]);
        $user->assignRole('staff');

        $this->post('/login', [
            'email'    => 'staff@test.com',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function manager_can_login_with_valid_credentials(): void
    {
        $manager = User::factory()->create([
            'email'     => 'manager@test.com',
            'password'  => Hash::make('securePass!'),
            'is_active' => true,
        ]);
        $manager->assignRole('manager');

        $this->post('/login', [
            'email'    => 'manager@test.com',
            'password' => 'securePass!',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($manager);
    }

    // ─── Login Failure ────────────────────────────────────────────────────────

    /** @test */
    public function login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email'    => 'staff@test.com',
            'password' => Hash::make('correctpassword'),
            'is_active' => true,
        ]);
        $user->assignRole('staff');

        $response = $this->post('/login', [
            'email'    => 'staff@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** @test */
    public function login_fails_with_nonexistent_email(): void
    {
        $this->post('/login', [
            'email'    => 'notexist@test.com',
            'password' => 'anypassword',
        ])->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    /** @test */
    public function inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email'     => 'inactive@test.com',
            'password'  => Hash::make('password123'),
            'is_active' => false,
        ]);
        $user->assignRole('staff');

        $this->post('/login', [
            'email'    => 'inactive@test.com',
            'password' => 'password123',
        ])->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    /** @test */
    public function login_requires_email_field(): void
    {
        $this->post('/login', [
            'password' => 'password123',
        ])->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function login_requires_password_field(): void
    {
        $this->post('/login', [
            'email' => 'test@test.com',
        ])->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function login_requires_valid_email_format(): void
    {
        $this->post('/login', [
            'email'    => 'not-an-email',
            'password' => 'password123',
        ])->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function login_requires_minimum_password_length(): void
    {
        $this->post('/login', [
            'email'    => 'test@test.com',
            'password' => 'short',
        ])->assertSessionHasErrors(['password']);
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    /** @test */
    public function authenticated_user_can_logout(): void
    {
        $user = $this->createStaff();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    /** @test */
    public function guest_cannot_logout(): void
    {
        $this->post('/logout')->assertRedirect(route('login'));
    }

    // ─── Protected Routes ─────────────────────────────────────────────────────

    /** @test */
    public function unauthenticated_user_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_access_dashboard(): void
    {
        $this->actingAs($this->createStaff())
            ->get('/dashboard')
            ->assertStatus(200);
    }

    /** @test */
    public function root_url_redirects_to_dashboard_when_authenticated(): void
    {
        $this->actingAs($this->createStaff())
            ->get('/')
            ->assertRedirect(route('dashboard'));
    }

    // ─── Rate Limiting ────────────────────────────────────────────────────────

    /** @test */
    public function login_is_rate_limited_after_five_attempts(): void
    {
        $user = User::factory()->create([
            'email'     => 'ratelimit@test.com',
            'password'  => Hash::make('correctpass'),
            'is_active' => true,
        ]);
        $user->assignRole('staff');

        // 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email'    => 'ratelimit@test.com',
                'password' => 'wrongpassword',
            ]);
        }

        // 6th attempt should be throttled
        $response = $this->post('/login', [
            'email'    => 'ratelimit@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}