<x-app-layout title="{{ $facility->exists ? 'Edit Facility' : 'Add Facility' }}">
    <div class="dashboard">
        <x-sidebar-component />
        <main class="dashboard-main">
            <header class="dashboard-header">
                <div>
                    <h1>{{ $facility->exists ? 'Edit Facility' : 'Add Facility' }}</h1>
                    <p class="dashboard-breadcrumb">Maintain facility and equipment information.</p>
                </div>
                <a class="filter-btn" href="{{ route('facilities.index') }}">Back to Facilities</a>
            </header>

            <form class="dashboard-panel admin-form" method="POST" action="{{ $facility->exists ? route('facilities.update', $facility) : route('facilities.store') }}">
                @csrf
                @if($facility->exists)
                    @method('PUT')
                @endif
                <div class="form-grid">
                    <label>Facility Code<input name="code" value="{{ old('code', $facility->code) }}" required maxlength="30"></label>
                    <label>Facility Name<input name="name" value="{{ old('name', $facility->name) }}" required maxlength="150"></label>
                    <label>Category<select name="category_id" required><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $facility->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                    <label>Location<select name="location_id" required><option value="">Select location</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected(old('location_id', $facility->location_id) == $location->id)>{{ $location->name }}</option>@endforeach</select></label>
                    <label>Condition<select name="condition_status"><option value="">Not specified</option>@foreach(['good', 'fair', 'poor'] as $condition)<option value="{{ $condition }}" @selected(old('condition_status', $facility->condition_status) === $condition)>{{ ucfirst($condition) }}</option>@endforeach</select></label>
                    <label>Status<select name="status" required>@foreach(['operational', 'needs_service', 'under_repair'] as $status)<option value="{{ $status }}" @selected(old('status', $facility->status ?: 'operational') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>@endforeach</select></label>
                    <label>Install Date<input type="date" name="install_date" value="{{ old('install_date', optional($facility->install_date)->format('Y-m-d')) }}"></label>
                    <label>Service Date<input type="date" name="service_date" value="{{ old('service_date', optional($facility->service_date)->format('Y-m-d')) }}"></label>
                    <label>Expiry Date<input type="date" name="expiry_date" value="{{ old('expiry_date', optional($facility->expiry_date)->format('Y-m-d')) }}"></label>
                    <label>QR Code<input name="qr_code" value="{{ old('qr_code', $facility->qr_code) }}" maxlength="100"></label>
                </div>
                @if($errors->any())<div class="form-error">Please correct the highlighted fields.</div>@endif
                <div class="form-actions"><a class="filter-btn" href="{{ route('facilities.index') }}">Cancel</a><button class="add-facility-btn" type="submit">{{ $facility->exists ? 'Save Changes' : 'Create Facility' }}</button></div>
            </form>
        </main>
    </div>
</x-app-layout>
