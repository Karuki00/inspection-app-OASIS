<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilitiesLocationsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_archive_a_facility(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@oasis.com')->firstOrFail();
        $category = AssetCategory::where('name', 'CCTV Camera')->firstOrFail();
        $location = Location::where('name', 'Main Lobby Entrance')->firstOrFail();

        $this->actingAs($admin)->post(route('facilities.store'), [
            'category_id' => $category->id,
            'location_id' => $location->id,
            'code' => 'TEST-001',
            'name' => 'Test Camera',
            'condition_status' => 'good',
            'status' => 'operational',
        ])->assertRedirect();

        $facility = Asset::where('code', 'TEST-001')->firstOrFail();

        $this->actingAs($admin)->get(route('facilities.edit', $facility))->assertOk();
        $this->actingAs($admin)->put(route('facilities.update', $facility), [
            'category_id' => $category->id,
            'location_id' => $location->id,
            'code' => 'TEST-001',
            'name' => 'Updated Test Camera',
            'condition_status' => 'fair',
            'status' => 'needs_service',
        ])->assertRedirect(route('facilities.index'));

        $this->actingAs($admin)->delete(route('facilities.destroy', $facility))->assertRedirect(route('facilities.index'));

        $this->assertDatabaseHas('assets', [
            'id' => $facility->id,
            'name' => 'Updated Test Camera',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_create_update_and_delete_an_unlinked_location(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@oasis.com')->firstOrFail();
        $parent = Location::where('name', 'Tower A')->firstOrFail();

        $this->actingAs($admin)->post(route('locations.store'), [
            'parent_location_id' => $parent->id,
            'name' => 'Test Floor',
            'type' => 'floor',
            'building_code' => 'TEST-FLOOR',
        ])->assertRedirect();

        $location = Location::where('name', 'Test Floor')->firstOrFail();

        $this->actingAs($admin)->get(route('locations.edit', $location))->assertOk();
        $this->actingAs($admin)->put(route('locations.update', $location), [
            'parent_location_id' => $parent->id,
            'name' => 'Updated Test Floor',
            'type' => 'floor',
            'building_code' => 'TEST-FLOOR-2',
        ])->assertRedirect(route('locations.index'));

        $this->actingAs($admin)->delete(route('locations.destroy', $location))->assertRedirect(route('locations.index'));
        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }

    public function test_admin_can_manage_categories_and_cannot_delete_a_category_in_use(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@oasis.com')->firstOrFail();
        $category = AssetCategory::create(['name' => 'Temporary Equipment', 'description' => 'Temporary test category']);

        $this->actingAs($admin)->get(route('facilities.categories.edit', $category))->assertOk();
        $this->actingAs($admin)->put(route('facilities.categories.update', $category), [
            'name' => 'Updated Equipment',
            'description' => 'Updated category description',
        ])->assertRedirect(route('facilities.index'));

        $this->actingAs($admin)->delete(route('facilities.categories.destroy', $category))->assertRedirect(route('facilities.index'));
        $this->assertDatabaseMissing('asset_categories', ['id' => $category->id]);

        $usedCategory = AssetCategory::where('name', 'Box Hydrant')->firstOrFail();

        $this->actingAs($admin)->delete(route('facilities.categories.destroy', $usedCategory))
            ->assertRedirect()
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('asset_categories', ['id' => $usedCategory->id]);
    }
}
