<x-app-layout title="{{ $location->exists ? 'Edit Location' : 'Add Location' }}">
    <div class="dashboard">
        <x-sidebar-component />
        <main class="dashboard-main">
            <header class="dashboard-header">
                <div>
                    <h1>{{ $location->exists ? 'Edit Location' : 'Add Location' }}</h1>
                    <p class="dashboard-breadcrumb">Organize the property inspection hierarchy.</p>
                </div>
                <a class="filter-btn" href="{{ route('locations.index') }}">Back to Locations</a>
            </header>

            <form class="dashboard-panel admin-form" method="POST" action="{{ $location->exists ? route('locations.update', $location) : route('locations.store') }}">
                @csrf
                @if($location->exists)
                    @method('PUT')
                @endif
                <div class="form-grid">
                    <label>Location Name<input name="name" value="{{ old('name', $location->name) }}" required maxlength="150"></label>
                    <label>Type<select name="type" required>@foreach(['apartment', 'tower', 'floor', 'area'] as $type)<option value="{{ $type }}" @selected(old('type', $location->type) === $type)>{{ ucfirst($type) }}</option>@endforeach</select></label>
                    <label>Parent Location<select name="parent_location_id"><option value="">No parent location</option>@foreach($parents as $parent)<option value="{{ $parent->id }}" @selected(old('parent_location_id', $location->parent_location_id) == $parent->id)>{{ $parent->name }}</option>@endforeach</select></label>
                    <label>Building Code<input name="building_code" value="{{ old('building_code', $location->building_code) }}" maxlength="30"></label>
                </div>
                @if($errors->any())<div class="form-error">Please correct the highlighted fields.</div>@endif
                <div class="form-actions"><a class="filter-btn" href="{{ route('locations.index') }}">Cancel</a><button class="add-facility-btn" type="submit">{{ $location->exists ? 'Save Changes' : 'Create Location' }}</button></div>
            </form>
        </main>
    </div>
</x-app-layout>
