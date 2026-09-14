<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilitiesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_facilities_page(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@oasis.com')->firstOrFail();

        $response = $this->actingAs($user)->get(route('facilities.index'));

        $response
            ->assertOk()
            ->assertSee('Facilities')
            ->assertSee('Total Facilities')
            ->assertSee('Operational')
            ->assertSee('Needs Maintenance')
            ->assertSee('Under Repair')
            ->assertSee('Box Hydrants')
            ->assertSee('HB-101')
            ->assertSee('Other Facility Categories')
            ->assertSee('CCTV Camera')
            ->assertSee('APAR');
    }
}
