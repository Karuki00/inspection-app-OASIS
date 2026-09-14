<?php

namespace Tests\Feature;

use App\Models\InspectionSchedule;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InspectionScheduleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_cancel_a_schedule(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@oasis.com')->firstOrFail();
        $officer = User::where('badge_id', 'SG-1024')->firstOrFail();
        $zone = Location::where('name', 'Main Lobby Entrance')->firstOrFail();

        $this->actingAs($admin)->post(route('inspection-schedules.store'), [
            'schedule_code' => 'SCH-TEST-001',
            'officer_id' => $officer->id,
            'patrol_zone_id' => $zone->id,
            'scheduled_date' => '2026-09-15',
            'shift_start' => '07:00',
            'shift_end' => '15:00',
            'status' => 'scheduled',
        ])->assertRedirect();

        $schedule = InspectionSchedule::where('schedule_code', 'SCH-TEST-001')->firstOrFail();

        $this->actingAs($admin)->get(route('inspection-schedules.edit', $schedule))->assertOk();
        $this->actingAs($admin)->put(route('inspection-schedules.update', $schedule), [
            'schedule_code' => 'SCH-TEST-001',
            'officer_id' => $officer->id,
            'patrol_zone_id' => $zone->id,
            'scheduled_date' => '2026-09-16',
            'shift_start' => '15:00',
            'shift_end' => '23:00',
            'status' => 'scheduled',
        ])->assertRedirect(route('inspection-schedules.index'));

        $this->actingAs($admin)->delete(route('inspection-schedules.destroy', $schedule))
            ->assertRedirect(route('inspection-schedules.index'));

        $this->assertDatabaseMissing('inspection_schedules', ['id' => $schedule->id]);
    }

    public function test_schedule_requires_shift_end_after_shift_start(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@oasis.com')->firstOrFail();
        $officer = User::where('badge_id', 'SG-1024')->firstOrFail();
        $zone = Location::where('name', 'Main Lobby Entrance')->firstOrFail();

        $this->actingAs($admin)->from(route('inspection-schedules.create'))->post(route('inspection-schedules.store'), [
            'schedule_code' => 'SCH-TEST-002',
            'officer_id' => $officer->id,
            'patrol_zone_id' => $zone->id,
            'scheduled_date' => '2026-09-15',
            'shift_start' => '15:00',
            'shift_end' => '07:00',
            'status' => 'scheduled',
        ])->assertRedirect(route('inspection-schedules.create'))
            ->assertSessionHasErrors('shift_end');
    }
}
