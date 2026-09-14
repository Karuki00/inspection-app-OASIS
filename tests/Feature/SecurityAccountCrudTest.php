<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAccountCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_security_account_assigned_to_a_location(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::create(['name' => 'Lobby', 'type' => 'area']);

        $this->actingAs($admin)
            ->post(route('security-accounts.store'), [
                'name' => 'Ayu Pratama',
                'badge_id' => 'SEC-100',
                'regu' => 'A',
                'email' => 'ayu@example.com',
                'phone' => '08123456789',
                'role' => 'danru',
                'shift' => 'day',
                'assigned_location_id' => $location->id,
                'employment_status' => 'active',
            ])
            ->assertRedirect();

        $account = User::where('email', 'ayu@example.com')->firstOrFail();

        $this->assertSame('danru', $account->role);
        $this->assertSame($location->id, $account->assigned_location_id);
        $this->assertNull($account->password);
    }

    public function test_admin_can_update_and_archive_a_security_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $account = User::factory()->create([
            'role' => 'security',
            'employment_status' => 'active',
            'password' => null,
        ]);

        $this->assertSame('security', $account->role);

        $this->actingAs($admin)
            ->put(route('security-accounts.update', $account), [
                'name' => 'Updated Officer',
                'badge_id' => 'SEC-101',
                'email' => $account->email,
                'role' => 'chief_security',
                'employment_status' => 'on_leave',
            ])
            ->assertRedirect(route('security-accounts.index'));

        $account->refresh();
        $this->assertSame('chief_security', $account->role);
        $this->assertSame('on_leave', $account->employment_status);
        $this->assertNull($account->password);

        $this->actingAs($admin)
            ->delete(route('security-accounts.destroy', $account))
            ->assertRedirect(route('security-accounts.index'));

        $this->assertDatabaseHas('users', [
            'id' => $account->id,
            'employment_status' => 'inactive',
        ]);
    }

    public function test_security_account_cannot_be_created_with_an_invalid_location(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('security-accounts.create'))
            ->post(route('security-accounts.store'), [
                'name' => 'Ayu Pratama',
                'email' => 'ayu@example.com',
                'role' => 'security',
                'assigned_location_id' => 999,
                'employment_status' => 'active',
            ])
            ->assertRedirect(route('security-accounts.create'))
            ->assertSessionHasErrors('assigned_location_id');
    }
}
