<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FacilityController extends Controller
{
    public function index(): View
    {
        $assets = Asset::query()->where('is_active', true)->with(['location', 'category'])->latest('id')->get();
        $stats = [
            ['label' => 'Total Facilities', 'value' => (string) Asset::where('is_active', true)->count(), 'meta' => 'Across all categories', 'tone' => 'green', 'icon' => '<path d="M3 15V4a1 1 0 0 1 1-1h9a1 1 0 0 1 1 1v11M1 15h15M6 6h1M10 6h1M6 9h1M10 9h1" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'Operational', 'value' => (string) Asset::where('is_active', true)->where('status', 'operational')->count(), 'meta' => 'Ready for use', 'tone' => 'green', 'icon' => '<path d="m4 8 3 3 5-6" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'Needs Maintenance', 'value' => (string) Asset::where('is_active', true)->where('status', 'needs_service')->count(), 'meta' => 'Scheduled for service', 'tone' => 'yellow', 'icon' => '<path d="m3 13 5-5 3 3 4-5" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'Under Repair', 'value' => (string) Asset::where('is_active', true)->where('status', 'under_repair')->count(), 'meta' => 'Work in progress', 'tone' => 'red', 'icon' => '<path d="M8 2 15 15H1L8 2Zm0 5v3m0 2v.01" stroke="currentColor" stroke-width="1.5"/>'],
        ];
        $facilities = $assets->map(fn (Asset $asset): array => ['id' => $asset->id, 'code' => $asset->code, 'name' => $asset->name, 'category' => $asset->category->name, 'location' => $asset->location->name, 'condition' => ucfirst((string) $asset->condition_status), 'condition_class' => $asset->condition_status ?? 'fair', 'status' => Str::headline((string) $asset->status), 'status_class' => $asset->status === 'operational' ? 'completed' : ($asset->status === 'needs_service' ? 'in-progress' : 'overdue')])->values()->all();
        $categories = AssetCategory::query()->withCount(['assets' => fn ($query) => $query->where('is_active', true)])->get()->map(fn (AssetCategory $category): array => ['id' => $category->id, 'name' => $category->name, 'description' => $category->description, 'count' => $category->assets_count.' units', 'tone' => 'green', 'icon' => '<circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/>'])->all();

        return view('admin.facilities.index', compact('stats', 'facilities', 'categories'));
    }

    public function create(): View
    {
        return view('admin.facilities.form', [
            'facility' => new Asset,
            'categories' => AssetCategory::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $asset = Asset::create($this->validatedData($request));

        return redirect()->route('facilities.edit', $asset)->with('status', 'Facility created successfully.');
    }

    public function edit(Asset $facility): View
    {
        return view('admin.facilities.form', [
            'facility' => $facility,
            'categories' => AssetCategory::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Asset $facility): RedirectResponse
    {
        $facility->update($this->validatedData($request, $facility));

        return redirect()->route('facilities.index')->with('status', 'Facility updated successfully.');
    }

    public function destroy(Asset $facility): RedirectResponse
    {
        $facility->update(['is_active' => false]);

        return redirect()->route('facilities.index')->with('status', 'Facility archived successfully.');
    }

    public function createCategory(): View
    {
        return view('admin.facilities.category-form', ['category' => new AssetCategory]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $category = AssetCategory::create($this->validatedCategoryData($request));

        return redirect()->route('facilities.categories.edit', $category)->with('status', 'Category created successfully.');
    }

    public function editCategory(AssetCategory $category): View
    {
        return view('admin.facilities.category-form', compact('category'));
    }

    public function updateCategory(Request $request, AssetCategory $category): RedirectResponse
    {
        $category->update($this->validatedCategoryData($request, $category));

        return redirect()->route('facilities.index')->with('status', 'Category updated successfully.');
    }

    public function destroyCategory(AssetCategory $category): RedirectResponse
    {
        if ($category->assets()->exists()) {
            return back()->withErrors(['category' => 'This category cannot be deleted while facilities are assigned to it.']);
        }

        $category->delete();

        return redirect()->route('facilities.index')->with('status', 'Category deleted successfully.');
    }

    private function validatedData(Request $request, ?Asset $facility = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:asset_categories,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'code' => ['required', 'string', 'max:30', Rule::unique('assets', 'code')->ignore($facility)],
            'name' => ['required', 'string', 'max:150'],
            'install_date' => ['nullable', 'date'],
            'service_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:install_date'],
            'condition_status' => ['nullable', Rule::in(['good', 'fair', 'poor'])],
            'status' => ['required', Rule::in(['operational', 'needs_service', 'under_repair'])],
            'qr_code' => ['nullable', 'string', 'max:100', Rule::unique('assets', 'qr_code')->ignore($facility)],
        ]) + ['is_active' => true];
    }

    private function validatedCategoryData(Request $request, ?AssetCategory $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('asset_categories', 'name')->ignore($category)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
