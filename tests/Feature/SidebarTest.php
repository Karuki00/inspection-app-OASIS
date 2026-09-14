<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_is_displayed(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@oasis.com')->firstOrFail();

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Inspections')
            ->assertSee('Reports');
    }
}
