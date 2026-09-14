<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAccountPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_security_accounts_page(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@oasis.com')->firstOrFail();

        $response = $this->actingAs($user)->get(route('security-accounts.index'));

        $response
            ->assertOk()
            ->assertSee('Security Accounts')
            ->assertSee('Manage security personnel assigned to the property')
            ->assertSee('TOTAL SECURITY PERSONNEL')
            ->assertSee('ACTIVE ON DUTY')
            ->assertSee('PENDING VERIFICATION')
            ->assertSee('Bambang Wijaya')
            ->assertSee('SG-1024')
            ->assertSee('Main Lobby Entrance')
            ->assertSee('Security Personnel');
    }
}
