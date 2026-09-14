@props(['personnel'])

<div class="security-table-wrap">
    <table class="schedule-table security-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>ID / Badge</th>
                <th>Phone</th>
                <th>Assigned Area / Post</th>
                <th>Shift</th>
                <th>Status</th>
                <th class="security-actions-heading">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($personnel as $guard)
                <tr>
                    <td><strong>{{ $guard->name }}</strong></td>
                    <td>{{ $guard->badge_id ?? 'No badge' }}</td>
                    <td>{{ $guard->phone ?? 'Not provided' }}</td>
                    <td><strong>{{ $guard->assignedLocation?->name ?? 'Unassigned' }}</strong></td>
                    <td>{{ $guard->shift ? ucfirst($guard->shift) : 'Unassigned' }}</td>
                    <td><x-badge :type="$guard->employment_status === 'active' ? 'active' : ($guard->employment_status === 'on_leave' ? 'on-leave' : 'inactive')" :label="str($guard->employment_status)->replace('_', ' ')->title()" /></td>
                    <td>
                        <div class="security-row-actions">
                            <a class="row-action edit" href="{{ route('security-accounts.edit', $guard) }}" aria-label="Edit {{ $guard->name }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20H4a1 1 0 01-1-1v-8M16.5 3.5a2.12 2.12 0 013 3L11 15l-4 1 1-4 8.5-8.5z" />
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('security-accounts.destroy', $guard) }}" onsubmit="return confirm('Archive {{ $guard->name }}? This will prevent the person from logging in.');">
                                @csrf
                                @method('DELETE')
                                <button class="row-action delete" type="submit" aria-label="Archive {{ $guard->name }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2m-9 0l1 14h8l1-14M10 10v7M14 10v7" />
                                </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No security accounts have been added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
