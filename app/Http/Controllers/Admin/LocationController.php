<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index(): View
    {
        $locationQuery = Location::query()->withCount('assets');
        $locations = $locationQuery->latest('id')->get()->map(function (Location $location): array {
            $status = $location->assets_count > 0 ? 'Active' : 'Inspection Due';

            return [
                'id' => $location->id,
                'code' => $location->building_code ?: 'LOC-'.str_pad((string) $location->id, 3, '0', STR_PAD_LEFT),
                'name' => $location->name,
                'type' => Str::headline($location->type),
                'facilities' => $location->assets_count.' facilities',
                'last_inspection' => 'Not recorded',
                'status' => $status,
                'status_class' => $status === 'Active' ? 'completed' : 'in-progress',
            ];
        })->all();

        $stats = [
            ['label' => 'TOTAL LOCATIONS', 'value' => (string) Location::count(), 'meta' => 'Across the property', 'tone' => 'green', 'icon' => '<circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'ACTIVE AREAS', 'value' => (string) Location::has('assets')->count(), 'meta' => 'Ready for inspection', 'tone' => 'green', 'icon' => '<path d="m4 8 3 3 7-7" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'UNDER INSPECTION', 'value' => '0', 'meta' => 'Inspections in progress', 'tone' => 'blue', 'icon' => '<circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'NEEDS ATTENTION', 'value' => (string) Location::whereHas('assets', fn ($query) => $query->where('status', '!=', 'operational'))->count(), 'meta' => 'Requires follow-up', 'tone' => 'red', 'icon' => '<path d="M8 5v3m0 3h.01M2.5 14h11L8 2 2.5 14Z" stroke="currentColor" stroke-width="1.5"/>'],
        ];

        $categories = collect(['tower' => 'Residential Towers', 'area' => 'Areas'])->map(function (string $name, string $type): array {
            return ['name' => $name, 'count' => Location::where('type', $type)->count().' locations', 'tone' => 'green'];
        })->values()->all();

        return view('admin.locations.index', compact('stats', 'locations', 'categories'));
    }

    public function show($id)
    {
        return view('admin.locations.show', compact('id'));
    }

    public function create(): View
    {
        return view('admin.locations.form', [
            'location' => new Location,
            'parents' => Location::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $location = Location::create($this->validatedData($request));

        return redirect()->route('locations.edit', $location)->with('status', 'Location created successfully.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.form', [
            'location' => $location,
            'parents' => Location::query()->whereKeyNot($location->id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $location->update($this->validatedData($request, $location));

        return redirect()->route('locations.index')->with('status', 'Location updated successfully.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->assets()->exists() || $location->children()->exists() || $location->assignedUsers()->exists()) {
            return back()->withErrors(['location' => 'This location cannot be deleted while it has linked records.']);
        }

        $location->delete();

        return redirect()->route('locations.index')->with('status', 'Location deleted successfully.');
    }

    private function validatedData(Request $request, ?Location $location = null): array
    {
        return $request->validate([
            'parent_location_id' => ['nullable', 'exists:locations,id', Rule::notIn([$location?->id])],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::in(['apartment', 'tower', 'floor', 'area'])],
            'building_code' => ['nullable', 'string', 'max:30'],
        ]);
    }
}
