<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\InspectionSchedule;
use App\Models\Problem;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            ['label' => 'Total Inspections', 'value' => (string) InspectionSchedule::count(), 'meta' => 'Schedules tracked', 'tone' => 'green', 'icon' => '<path d="m4 8 3 3 5-6" stroke="currentColor" stroke-width="2"/>'],
            ['label' => 'Pending Issues', 'value' => (string) Problem::whereIn('status', ['open', 'in_progress', 'escalated'])->count(), 'meta' => 'Open and active issues', 'tone' => 'red', 'icon' => '<path d="M8 5v3m0 3h.01M2.5 14h11L8 2 2.5 14Z" stroke="currentColor" stroke-width="2"/>'],
            ['label' => 'Upcoming Schedules', 'value' => (string) InspectionSchedule::whereIn('status', ['scheduled', 'in_progress'])->count(), 'meta' => 'Assigned schedules', 'tone' => 'blue', 'icon' => '<path d="M5 1v3M11 1v3M2 7h12M3 2h10a1 1 0 0 1 1 1v11H2V3a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="2"/>'],
            ['label' => 'Facilities Managed', 'value' => (string) Asset::count(), 'meta' => 'Tracked assets', 'tone' => 'yellow', 'icon' => '<path d="M3 15V4a1 1 0 0 1 1-1h9v12M1 15h15" stroke="currentColor" stroke-width="2"/>'],
        ];
        $schedules = InspectionSchedule::with(['officer', 'patrolZone'])->latest('scheduled_date')->take(6)->get()->map(fn (InspectionSchedule $schedule): array => ['schedule_id' => $schedule->schedule_code, 'facility' => 'Security patrol', 'location' => $schedule->patrolZone->name, 'inspector' => $schedule->officer->name, 'date' => $schedule->scheduled_date->format('M d, Y'), 'status' => ucwords(str_replace('_', ' ', $schedule->status)), 'status_class' => $schedule->status === 'completed' ? 'completed' : ($schedule->status === 'scheduled' ? 'scheduled' : ($schedule->status === 'in_progress' ? 'in-progress' : 'overdue'))])->all();
        $problems = Problem::with('location')->latest()->take(4)->get()->map(fn (Problem $problem): array => ['title' => $problem->description, 'location' => $problem->location->name, 'severity' => ucfirst($problem->severity), 'severity_class' => $problem->severity, 'updated' => $problem->updated_at->diffForHumans()])->all();
        $health = [['label' => 'Overall Facility Status', 'value' => Asset::where('status', 'operational')->count().' operational', 'detail' => 'Operational'], ['label' => 'Open Problems', 'value' => Problem::whereIn('status', ['open', 'in_progress'])->count(), 'detail' => 'Tracked'], ['label' => 'Resolved Problems', 'value' => Problem::where('status', 'resolved')->count(), 'detail' => 'Improving'], ['label' => 'Preventive Inspections', 'value' => InspectionSchedule::where('status', 'scheduled')->count(), 'detail' => 'Scheduled']];

        return view('admin.dashboard.index', compact('stats', 'schedules', 'problems', 'health'));
    }
}
