<x-app-layout title="{{ $category->exists ? 'Edit Facility Category' : 'Add Facility Category' }}">
    <div class="dashboard">
        <x-sidebar-component />
        <main class="dashboard-main">
            <header class="dashboard-header">
                <div>
                    <h1>{{ $category->exists ? 'Edit Facility Category' : 'Add Facility Category' }}</h1>
                    <p class="dashboard-breadcrumb">Create reusable categories for facilities and equipment.</p>
                </div>
                <a class="filter-btn" href="{{ route('facilities.index') }}">Back to Facilities</a>
            </header>

            <form class="dashboard-panel admin-form category-form" method="POST" action="{{ $category->exists ? route('facilities.categories.update', $category) : route('facilities.categories.store') }}">
                @csrf
                @if($category->exists)
                    @method('PUT')
                @endif
                <div class="form-grid">
                    <label>Category Name<input name="name" value="{{ old('name', $category->name) }}" required maxlength="100" placeholder="e.g. Fire Alarm System"></label>
                    <label>Description<textarea name="description" maxlength="255" placeholder="What type of facilities belong here?">{{ old('description', $category->description) }}</textarea></label>
                </div>
                @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
                <div class="form-actions"><a class="filter-btn" href="{{ route('facilities.index') }}">Cancel</a><button class="add-facility-btn" type="submit">{{ $category->exists ? 'Save Changes' : 'Create Category' }}</button></div>
            </form>
        </main>
    </div>
</x-app-layout>
