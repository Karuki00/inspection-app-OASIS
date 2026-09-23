<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ProblemController extends Controller
{
    public function index(): View
    {
        $problems = Problem::with(['location', 'assignedTo'])->latest()->get();
        $rows = $problems->map(fn (Problem $problem): array => ['code' => $problem->problem_code, 'location' => $problem->location->name, 'category' => Str::headline($problem->category), 'severity' => ucfirst($problem->severity), 'severity_class' => $problem->severity, 'reported' => $problem->created_at->format('M d, Y'), 'assigned' => $problem->assignedTo?->name ?? 'Unassigned', 'status' => Str::headline($problem->status), 'status_class' => $problem->status === 'resolved' ? 'completed' : ($problem->status === 'in_progress' ? 'in-progress' : ($problem->status === 'escalated' ? 'scheduled' : 'overdue')), 'days_open' => (string) $problem->created_at->diffInDays($problem->resolved_at ?? now())])->all();
        $stats = [
            ['label' => 'TOTAL PROBLEMS', 'value' => (string) Problem::count(), 'meta' => 'Across all categories', 'tone' => 'yellow', 'icon' => '<path d="M8 5v3m0 3h.01M2.5 14h11L8 2 2.5 14Z" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'OPEN ISSUES', 'value' => (string) Problem::whereIn('status', ['open', 'escalated'])->count(), 'meta' => 'Open and escalated', 'tone' => 'red', 'icon' => '<path d="M8 5v3m0 3h.01M2.5 14h11L8 2 2.5 14Z" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'IN PROGRESS', 'value' => (string) Problem::where('status', 'in_progress')->count(), 'meta' => 'Active work orders', 'tone' => 'yellow', 'icon' => '<circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'RESOLVED THIS MONTH', 'value' => (string) Problem::where('status', 'resolved')->whereMonth('resolved_at', now()->month)->count(), 'meta' => 'Closed successfully', 'tone' => 'green', 'icon' => '<path d="m4 8 2 2 5-5" stroke="currentColor" stroke-width="1.5"/>'],
        ];
        $severitySummary = collect(['critical', 'high', 'medium', 'low'])->map(fn (string $severity): array => ['label' => ucfirst($severity), 'count' => (string) Problem::where('severity', $severity)->count(), 'tone' => $severity])->all();
        $resolutionTimeline = collect(['electrical', 'plumbing', 'fire_safety', 'structural'])->map(function (string $category): array {
            $resolvedProblems = Problem::where('category', $category)->whereNotNull('resolved_at')->get();
            $averageDays = $resolvedProblems->isEmpty() ? 0 : $resolvedProblems->avg(fn (Problem $problem): int => $problem->created_at->diffInDays($problem->resolved_at));

            return ['label' => Str::headline($category), 'days' => number_format((float) $averageDays, 1).' days', 'width_class' => 'width-48'];
        })->all();

        return view('admin.problem-statuses.index', ['stats' => $stats, 'problems' => $rows, 'severitySummary' => $severitySummary, 'resolutionTimeline' => $resolutionTimeline]);
    }
}
