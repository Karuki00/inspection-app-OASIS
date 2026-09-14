<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@oasis.com')->firstOrFail();

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Total Inspections')
            ->assertSee('Schedules tracked')
            ->assertSee('Pending Issues')
            ->assertSee('Open and active issues')
            ->assertSee('Upcoming Schedules')
            ->assertSee('Assigned schedules')
            ->assertSee('Facilities Managed')
            ->assertSee('Tracked assets')
            ->assertSee('Upcoming Inspection Schedules')
            ->assertSee('Reported Facility Issues')
            ->assertSee('Facility Health');
    }
}
