<x-app-layout title="{{ $schedule->exists ? 'Edit Inspection Schedule' : 'Add Inspection Schedule' }}">
    <div class="dashboard">
        <x-sidebar-component />
        <main class="dashboard-main">
            <header class="dashboard-header">
                <div>
                    <h1>{{ $schedule->exists ? 'Edit Inspection Schedule' : 'Add Inspection Schedule' }}</h1>
                    <p class="dashboard-breadcrumb">Assign an officer to a patrol zone and inspection shift.</p>
                </div>
                <a class="filter-btn" href="{{ route('inspection-schedules.index') }}">Back to Schedules</a>
            </header>

            <form class="dashboard-panel admin-form" method="POST" action="{{ $schedule->exists ? route('inspection-schedules.update', $schedule) : route('inspection-schedules.store') }}">
                @csrf
                @if($schedule->exists)
                    @method('PUT')
                @endif
                <div class="form-grid">
                    <label>Schedule Code<input name="schedule_code" value="{{ old('schedule_code', $schedule->schedule_code) }}" required maxlength="30" placeholder="SCH-2026-001"></label>
                    <label>Security Officer<select name="officer_id" required><option value="">Select officer</option>@foreach($officers as $officer)<option value="{{ $officer->id }}" @selected(old('officer_id', $schedule->officer_id) == $officer->id)>{{ $officer->name }}{{ $officer->badge_id ? ' ('.$officer->badge_id.')' : '' }}</option>@endforeach</select></label>
                    <label>Patrol Zone<select name="patrol_zone_id" required><option value="">Select location</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" @selected(old('patrol_zone_id', $schedule->patrol_zone_id) == $zone->id)>{{ $zone->name }}</option>@endforeach</select></label>
                    <label>Scheduled Date<input type="date" name="scheduled_date" value="{{ old('scheduled_date', optional($schedule->scheduled_date)->format('Y-m-d')) }}" required></label>
                    <label>Shift Start<input type="time" name="shift_start" value="{{ old('shift_start', $schedule->shift_start?->format('H:i')) }}" required></label>
                    <label>Shift End<input type="time" name="shift_end" value="{{ old('shift_end', $schedule->shift_end?->format('H:i')) }}" required></label>
                    <label>Status<select name="status" required>@foreach(['scheduled', 'in_progress', 'completed', 'overdue', 'missed'] as $status)<option value="{{ $status }}" @selected(old('status', $schedule->status) === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>@endforeach</select></label>
                </div>
                @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
                <div class="form-actions"><a class="filter-btn" href="{{ route('inspection-schedules.index') }}">Cancel</a><button class="add-facility-btn" type="submit">{{ $schedule->exists ? 'Save Changes' : 'Create Schedule' }}</button></div>
            </form>
        </main>
    </div>
</x-app-layout>
