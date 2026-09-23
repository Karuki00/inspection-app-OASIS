<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SecurityAccountController extends Controller
{
    public function index(): View
    {
        $securityRoles = $this->securityRoles();

        $personnel = User::query()
            ->whereIn('role', $securityRoles)
            ->with('assignedLocation')
            ->orderBy('name')
            ->get();

        $stats = [
            [
                'label' => 'TOTAL SECURITY PERSONNEL',
                'value' => (string) count($personnel),
                'meta' => User::whereIn('role', $securityRoles)
                    ->where('employment_status', 'active')
                    ->count().' active on duty',
                'tone' => 'green',
                'icon' => '<path d="M9 12l2 2 4-4m5.6-4A12 12 0 0 1 12 3a12 12 0 0 1-8.6 3A12 12 0 0 0 3 9c0 5.6 3.8 10.3 9 11.6C17.2 19.3 21 14.6 21 9c0-1-.1-2-.4-3Z" stroke="currentColor" stroke-width="2"/>',
            ],
            [
                'label' => 'ACTIVE ON DUTY',
                'value' => (string) User::where('employment_status', 'active')
                    ->whereIn('role', $securityRoles)
                    ->count(),
                'meta' => 'Current personnel',
                'tone' => 'green',
                'icon' => '<path d="M5 1v3M11 1v3M2 7h12M3 2h10a1 1 0 0 1 1 1v11H2V3a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.5"/>',
            ],
            [
                'label' => 'OFF DUTY',
                'value' => (string) User::whereIn('employment_status', ['on_leave', 'inactive'])
                    ->whereIn('role', $securityRoles)
                    ->count(),
                'meta' => 'Leave or rest',
                'tone' => 'yellow',
                'icon' => '<circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5"/><path d="M8 5v3l2 1" stroke="currentColor" stroke-width="1.5"/>',
            ],
            [
                'label' => 'PENDING VERIFICATION',
                'value' => (string) User::whereNull('badge_id')
                    ->whereIn('role', $securityRoles)
                    ->count(),
                'meta' => 'Need ID / badge confirmation',
                'tone' => 'red',
                'icon' => '<path d="M8 5v3m0 3h.01M2.5 14h11L8 2 2.5 14Z" stroke="currentColor" stroke-width="1.5"/>',
            ],
        ];

        return view('admin.security-accounts.index', compact('stats', 'personnel'));
    }

    public function create(): View
    {
        return view('admin.security-accounts.form', [
            'user' => new User,
            'locations' => Location::query()->orderBy('name')->get(),
            'securityRoles' => $this->securityRoles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = User::create($this->validatedData($request));

        return redirect()
            ->route('security-accounts.edit', $user)
            ->with('status', 'Security account created successfully.');
    }

    public function edit(User $user): View
    {
        $this->ensureSecurityAccount($user);

        return view('admin.security-accounts.form', [
            'user' => $user,
            'locations' => Location::query()->orderBy('name')->get(),
            'securityRoles' => $this->securityRoles(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureSecurityAccount($user);

        $user->update($this->validatedData($request, $user));

        return redirect()
            ->route('security-accounts.index')
            ->with('status', 'Security account updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->ensureSecurityAccount($user);

        if ($request->user()?->is($user)) {
            return back()->withErrors(['security_account' => 'You cannot archive your own account.']);
        }

        $user->update([
            'employment_status' => 'inactive',
        ]);

        return redirect()
            ->route('security-accounts.index')
            ->with('status', 'Security account archived successfully.');
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'badge_id' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'badge_id')->ignore($user),
            ],
            'regu' => ['nullable', Rule::in(['A', 'B', 'C', 'D'])],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in($this->securityRoles())],
            'shift' => ['nullable', Rule::in(['day', 'night'])],
            'assigned_location_id' => [
                'nullable',
                'exists:locations,id',
            ],
            'employment_status' => [
                'required',
                Rule::in(['active', 'on_leave', 'inactive']),
            ],
        ]);

        $data['password'] = null;

        return $data;
    }

    /**
     * @return array<int, string>
     */
    private function securityRoles(): array
    {
        return ['security', 'danru', 'chief_security'];
    }

    private function ensureSecurityAccount(User $user): void
    {
        abort_unless(in_array($user->role, $this->securityRoles(), true), 404);
    }
}
