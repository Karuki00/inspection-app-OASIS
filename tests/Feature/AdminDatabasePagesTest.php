<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\InspectionSchedule;
use App\Models\Location;
use App\Models\Problem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDatabasePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_domain_records_are_linked(): void
    {
        $this->seed();

        $this->assertGreaterThan(0, Location::count());
        $this->assertGreaterThan(0, Asset::count());
        $this->assertGreaterThan(0, InspectionSchedule::count());
        $this->assertGreaterThan(0, Problem::count());
        $this->assertNotNull(User::where('email', 'admin@oasis.com')->first());
        $this->assertNotNull(Asset::first()->location);
        $this->assertNotNull(Problem::first()->location);
    }

    public function test_database_backed_admin_pages_render(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@oasis.com')->firstOrFail();

        foreach (['dashboard.index', 'facilities.index', 'inspection-schedules.index', 'locations.index', 'inspection-reports.index', 'problems.index', 'security-accounts.index'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }
}
