<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_expected_form_elements(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Welcome Back')
            ->assertSee('Please enter your credentials to access the OASIS portal.')
            ->assertSee('Email or Username')
            ->assertSee('name="login"', false)
            ->assertSee('name="password"', false)
            ->assertSee('onclick="togglePasswordVisibility()"', false)
            ->assertSee('Remember Me')
            ->assertSee('Log In', false)
            ->assertSee('action="'.route('login.submit').'"', false);
    }

    public function test_forgot_password_page_renders(): void
    {
        $response = $this->get(route('password.request'));

        $response
            ->assertOk()
            ->assertSee('Forgot Password')
            ->assertSee('Email')
            ->assertSee('Send Password Reset Link', false);
    }

    public function test_admin_is_redirected_to_the_dashboard_after_logging_in(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@oasis.com',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'employment_status' => 'active',
        ]);

        $response = $this->from('/')->post(route('login.submit'), [
            'login' => $user->email,
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('dashboard.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_security_officer_cannot_log_in_to_the_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Security Officer',
            'email' => 'security@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'security',
            'employment_status' => 'active',
        ]);

        $this->from(route('login'))
            ->post(route('login.submit'), [
                'login' => $user->email,
                'password' => 'Password123!',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_user_cannot_log_in_with_wrong_password(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@oasis.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->from('/')->post(route('login.submit'), [
            'login' => $user->email,
            'password' => 'WrongPassword!',
        ]);

        $response
            ->assertRedirect('/')
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }
}
