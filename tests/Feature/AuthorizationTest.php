<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_pages(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@oasis.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard.index'))
            ->assertOk();
    }

    public function test_security_user_cannot_access_admin_pages(): void
    {
        $user = User::create([
            'name' => 'Security Officer',
            'email' => 'security@example.com',
            'password' => Hash::make('password'),
            'role' => 'security',
            'employment_status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertForbidden();
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::create([
            'name' => 'Inactive Officer',
            'email' => 'inactive@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'employment_status' => 'inactive',
        ]);

        $this->from(route('login'))
            ->post(route('login.submit'), [
                'login' => $user->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_dev_login_route_is_not_available(): void
    {
        $this->get('/dev-login')->assertNotFound();
    }
}
