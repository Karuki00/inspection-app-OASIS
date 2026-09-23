<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InspectionSchedule;
use App\Models\Location;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InspectionScheduleController extends Controller
{
    public function index(): View
    {
        $query = InspectionSchedule::query()->with(['officer', 'patrolZone'])->latest('scheduled_date');
        $scheduleRecords = $query->get();
        $schedules = $scheduleRecords->map(fn (InspectionSchedule $schedule): array => ['id' => $schedule->id, 'code' => $schedule->schedule_code, 'officer' => $schedule->officer->name, 'zone' => $schedule->patrolZone->name, 'date' => $schedule->scheduled_date->format('M d, Y'), 'time' => substr((string) $schedule->shift_start, 0, 5).'-'.substr((string) $schedule->shift_end, 0, 5), 'status' => Str::headline($schedule->status), 'status_class' => $schedule->status === 'completed' ? 'completed' : ($schedule->status === 'scheduled' ? 'scheduled' : ($schedule->status === 'in_progress' ? 'in-progress' : 'overdue'))])->all();
        $stats = [
            ['label' => 'TOTAL SCHEDULES', 'value' => (string) InspectionSchedule::count(), 'meta' => 'Across all zones', 'tone' => 'green', 'icon' => '<path d="M5 1v3M11 1v3M2 7h12M3 2h10a1 1 0 0 1 1 1v11H2V3a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'COMPLETED THIS MONTH', 'value' => (string) InspectionSchedule::where('status', 'completed')->count(), 'meta' => 'Completed schedules', 'tone' => 'green', 'icon' => '<path d="m4 8 3 3 5-6" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'UPCOMING', 'value' => (string) InspectionSchedule::whereIn('status', ['scheduled', 'in_progress'])->count(), 'meta' => 'Scheduled or active', 'tone' => 'blue', 'icon' => '<path d="M5 1v3M11 1v3M2 7h12M3 2h10a1 1 0 0 1 1 1v11H2V3a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'OVERDUE', 'value' => (string) InspectionSchedule::whereIn('status', ['overdue', 'missed'])->count(), 'meta' => 'Requires immediate follow-up', 'tone' => 'red', 'icon' => '<path d="M8 5v3m0 3h.01M2.5 14h11L8 2 2.5 14Z" stroke="currentColor" stroke-width="1.5"/>'],
        ];
        $reports = $scheduleRecords->take(2)->map(fn (InspectionSchedule $schedule): array => ['title' => $schedule->patrolZone->name.' Patrol', 'detail' => 'Officer '.$schedule->officer->name.' - '.$schedule->scheduled_date->format('M d, Y'), 'status' => $schedule->status === 'completed' ? 'All Clear' : 'Issue Found', 'status_class' => $schedule->status === 'completed' ? 'completed' : 'overdue'])->all();

        return view('admin.inspection-schedules.index', compact('stats', 'schedules', 'reports'));
    }

    public function create(): View
    {
        return view('admin.inspection-schedules.form', [
            'schedule' => new InspectionSchedule(['status' => 'scheduled']),
            'officers' => $this->officers(),
            'zones' => Location::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $schedule = InspectionSchedule::create($this->validatedData($request));

        return redirect()->route('inspection-schedules.edit', $schedule)->with('status', 'Inspection schedule created successfully.');
    }

    public function edit(InspectionSchedule $inspection_schedule): View
    {
        return view('admin.inspection-schedules.form', [
            'schedule' => $inspection_schedule,
            'officers' => $this->officers(),
            'zones' => Location::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, InspectionSchedule $inspection_schedule): RedirectResponse
    {
        $inspection_schedule->update($this->validatedData($request, $inspection_schedule));

        return redirect()->route('inspection-schedules.index')->with('status', 'Inspection schedule updated successfully.');
    }

    public function destroy(InspectionSchedule $inspection_schedule): RedirectResponse
    {
        if (! in_array($inspection_schedule->status, ['scheduled', 'overdue', 'missed'], true)) {
            return back()->withErrors(['schedule' => 'Only scheduled or overdue inspections can be cancelled.']);
        }

        $inspection_schedule->delete();

        return redirect()->route('inspection-schedules.index')->with('status', 'Inspection schedule cancelled successfully.');
    }

    private function officers()
    {
        return User::query()
            ->whereIn('role', ['security', 'danru', 'chief_security'])
            ->where('employment_status', 'active')
            ->orderBy('name')
            ->get();
    }

    private function validatedData(Request $request, ?InspectionSchedule $schedule = null): array
    {
        return $request->validate([
            'schedule_code' => ['required', 'string', 'max:30', Rule::unique('inspection_schedules', 'schedule_code')->ignore($schedule)],
            'officer_id' => ['required', 'exists:users,id'],
            'patrol_zone_id' => ['required', 'exists:locations,id'],
            'scheduled_date' => ['required', 'date'],
            'shift_start' => ['required', 'date_format:H:i'],
            'shift_end' => ['required', 'date_format:H:i', 'after:shift_start'],
            'status' => ['required', Rule::in(['scheduled', 'in_progress', 'completed', 'overdue', 'missed'])],
        ]);
    }
}
