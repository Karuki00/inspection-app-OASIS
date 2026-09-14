<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InspectionSchedulePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_inspection_schedules_page(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@oasis.com')->firstOrFail();

        $response = $this->actingAs($user)->get(route('inspection-schedules.index'));

        $response
            ->assertOk()
            ->assertSee('Inspection Schedules')
            ->assertSee('TOTAL SCHEDULES')
            ->assertSee('COMPLETED THIS MONTH')
            ->assertSee('UPCOMING')
            ->assertSee('OVERDUE')
            ->assertSee('Security Inspection Schedules')
            ->assertSee('SCH-2401')
            ->assertSee('Bambang Wijaya')
            ->assertSee('Recent Reports')
            ->assertSee('Rooftop &amp; Sky Lounge Patrol', false)
            ->assertSee('Level 5 Recreation &amp; Pool Patrol', false);
    }
}
