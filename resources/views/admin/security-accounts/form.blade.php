<x-app-layout title="{{ $user->exists ? 'Edit Security Account' : 'Add Security Account' }}">
    <div class="dashboard">
        <x-sidebar-component />
        <main class="dashboard-main">
            <header class="dashboard-header">
                <div>
                    <h1>{{ $user->exists ? 'Edit Security Account' : 'Add Security Account' }}</h1>
                    <p class="dashboard-breadcrumb"> {{ $user->exists     ? 'Update security personnel account information.'     : 'Create a new account for security personnel.' }}</p>
                </div>

                <a class="filter-btn" href="{{ route('security-accounts.index') }}"> Back to Security Accounts</a>
            </header>


            <form class="dashboard-panel admin-form" method="POST" action="{{ $user->exists ? route('security-accounts.update', $user): route('security-accounts.store') }}">
                @csrf
                @if($user->exists)
                @method('PUT')
                @endif
                <div class="form-grid">
                    <label> Full Name <input type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="255" placeholder="Enter full name"> </label>
                    <label> Badge ID <input type="text" name="badge_id" value="{{ old('badge_id', $user->badge_id) }}" maxlength="30" placeholder="e.g. SEC-001"> </label>
                    <label> Regulation <select name="regu"> <option value="">Select regulation</option> @foreach(['A', 'B', 'C', 'D'] as $regu) <option value="{{ $regu }}" @selected(old('regu', $user->regu) === $regu) > {{ $regu }} </option> @endforeach </select> </label>
                    <label> Email <input type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255" placeholder="security@example.com"> </label>
                    <label> Phone Number <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="30" placeholder="08xxxxxxxxxx"> </label>
                    <label> Role <select name="role" required> @foreach($securityRoles as $role) <option value="{{ $role }}" @selected(old('role', $user->role ?: 'security') === $role)>{{ str($role)->headline() }}</option> @endforeach </select> </label>
                    <label> Shift <select name="shift"> <option value="">Select shift</option> <option value="day" @selected(old('shift', $user->shift) === 'day') > Day </option> <option value="night" @selected(old('shift', $user->shift) === 'night') > Night </option></select> </label>
                    <label> Assigned Location <select name="assigned_location_id"> <option value="">No location assigned</option> @foreach($locations as $location) <option value="{{ $location->id }}" @selected( old('assigned_location_id', $user->assigned_location_id) == $location->id ) > {{ $location->name }}</option> @endforeach </select> </label>
                    <label> Employment Status <select name="employment_status" required> @foreach(['active', 'on_leave', 'inactive'] as $status) <option value="{{ $status }}" @selected( old( 'employment_status' , $user->employment_status ?: 'active' ) === $status ) > {{ ucwords(str_replace('_', ' ', $status)) }} </option> @endforeach </select> </label>
                    <p class="form-help">Security personnel are data identities only. They do not log in to the admin dashboard; imported reports identify them by their security account.</p>
                </div>
                @if($errors->any())
                <div class="form-error"> Please correct the highlighted fields.
                </div>
                @endif
                <div class="form-actions">
                    <a class="filter-btn" href="{{ route('security-accounts.index') }}"> Cancel
                    </a>
                    <button class="add-facility-btn" type="submit"> {{ $user->exists ? 'Save Changes' : 'Create Account' }}
                    </button>
                </div>
            </form>
        </main>
    </div>
</x-app-layout>
