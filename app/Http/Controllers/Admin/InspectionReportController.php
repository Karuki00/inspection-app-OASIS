<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\Problem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use JsonException;
use ZipArchive;

class InspectionReportController extends Controller
{
    public function index(): View
    {
        $inspections = Inspection::with(['asset.location', 'inspector'])->latest('inspected_at')->get();
        $reports = $inspections->map(fn (Inspection $inspection): array => ['id' => $inspection->id, 'code' => 'REP-'.str_pad((string) $inspection->id, 4, '0', STR_PAD_LEFT), 'facility' => $inspection->asset->name, 'inspector' => $inspection->inspector->name, 'date' => $inspection->inspected_at->format('M d, Y'), 'type' => Str::headline($inspection->type), 'type_class' => $inspection->type === 'emergency' ? 'overdue' : 'completed', 'findings' => (string) $inspection->problems()->count(), 'status' => Str::headline($inspection->review_status), 'status_class' => $inspection->review_status === 'approved' ? 'completed' : ($inspection->review_status === 'rejected' ? 'overdue' : 'in-progress')])->all();
        $stats = [
            ['label' => 'TOTAL REPORTS', 'value' => (string) Inspection::count(), 'meta' => 'Across all facilities', 'tone' => 'yellow', 'icon' => '<path d="M5 2h6l3 3v10H5a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'REPORTS THIS MONTH', 'value' => (string) Inspection::whereMonth('inspected_at', now()->month)->count(), 'meta' => 'Current month', 'tone' => 'green', 'icon' => '<path d="M4 2v3M10 2v3M2 7h10M3 3h8a1 1 0 0 1 1 1v9H2V4a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'PENDING REVIEWS', 'value' => (string) Inspection::where('review_status', 'pending_review')->count(), 'meta' => 'Awaiting manager approval', 'tone' => 'yellow', 'icon' => '<path d="M8 5v3m0 3h.01M2.5 14h11L8 2 2.5 14Z" stroke="currentColor" stroke-width="1.5"/>'],
            ['label' => 'CRITICAL FINDINGS', 'value' => (string) Inspection::whereHas('problems', fn ($query) => $query->where('severity', 'critical'))->count(), 'meta' => 'High-risk inspections', 'tone' => 'red', 'icon' => '<path d="M8 5v3m0 3h.01M2.5 14h11L8 2 2.5 14Z" stroke="currentColor" stroke-width="1.5"/>'],
        ];
        $categories = [];
        $activity = collect($reports)->take(2)->map(fn (array $report): array => ['text' => $report['code'].' '.$report['status'], 'time' => $report['date'], 'status' => $report['status'], 'status_class' => $report['status_class']])->all();

        return view('admin.inspection-reports.index', compact('stats', 'reports', 'categories', 'activity'));
    }

    public function import(Request $request): JsonResponse
    {
        $data = $this->validateReportData($request->all());

        return response()->json($this->persistReport($data));
    }

    public function importZip(Request $request): RedirectResponse
    {
        $request->validate([
            'report_zip' => ['required', 'file', 'mimes:zip', 'max:10240'],
        ]);

        $file = $request->file('report_zip');
        $directory = storage_path('app/private/report-imports/'.Str::uuid());
        $zip = new ZipArchive;

        try {
            if (! $file instanceof UploadedFile || $zip->open($file->getRealPath()) !== true) {
                return back()->withErrors(['report_zip' => 'The ZIP file could not be opened.']);
            }

            foreach (range(0, $zip->numFiles - 1) as $index) {
                $entry = $zip->getNameIndex($index);

                if ($entry !== false && (Str::startsWith($entry, ['/', '\\']) || str_contains($entry, '..'))) {
                    return back()->withErrors(['report_zip' => 'The ZIP contains an unsafe file path.']);
                }
            }

            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                return back()->withErrors(['report_zip' => 'The report import workspace could not be created.']);
            }

            $zip->extractTo($directory);
            $jsonFiles = glob($directory.'/*.json');

            if ($jsonFiles === false || count($jsonFiles) !== 1) {
                return back()->withErrors(['report_zip' => 'The ZIP must contain exactly one JSON report file.']);
            }

            $json = file_get_contents($jsonFiles[0]);

            if ($json === false) {
                return back()->withErrors(['report_zip' => 'The JSON report could not be read.']);
            }

            try {
                $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                return back()->withErrors(['report_zip' => 'The JSON report is invalid.']);
            }

            $data = $this->validateReportData(is_array($payload) ? $payload : []);
            $result = $this->persistReport($data);

            return back()->with('status', 'Inspection report imported successfully ('.$result['items'].' checks, '.$result['problems'].' problems).');
        } finally {
            $zip->close();
            if (is_dir($directory)) {
                $this->removeImportDirectory($directory);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function validateReportData(array $input): array
    {
        return Validator::make($input, [
            'asset_id' => ['required', 'exists:assets,id'],
            'inspector_id' => ['required', 'exists:users,id'],
            'inspected_at' => ['required', 'date_format:Y-m-d H:i:s'],
            'type' => ['required', Rule::in(['routine', 'emergency', 'follow_up'])],
            'notes' => ['nullable', 'string'],
            'review_status' => ['nullable', Rule::in(['pending_review', 'approved', 'rejected'])],
            'reviewed_by' => ['nullable', 'exists:users,id'],
            'reviewed_at' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'items' => ['nullable', 'array'],
            'items.*.component_name' => ['required_with:items.*', 'string', 'max:50'],
            'items.*.condition_code' => ['required_with:items.*', Rule::in(['V', 'X', 'R'])],
            'items.*.remark' => ['nullable', 'string', 'max:255'],
            'problems' => ['nullable', 'array'],
            'problems.*.problem_code' => ['nullable', 'string', 'max:30', 'unique:problems,problem_code'],
            'problems.*.location_id' => ['required_with:problems.*', 'exists:locations,id'],
            'problems.*.category' => ['required_with:problems.*', Rule::in(['electrical', 'plumbing', 'fire_safety', 'hvac', 'structural', 'general'])],
            'problems.*.severity' => ['required_with:problems.*', Rule::in(['critical', 'high', 'medium', 'low'])],
            'problems.*.description' => ['required_with:problems.*', 'string'],
            'problems.*.reported_by' => ['required_with:problems.*', 'exists:users,id'],
            'problems.*.assigned_to' => ['nullable', 'exists:users,id'],
            'problems.*.status' => ['required_with:problems.*', Rule::in(['open', 'in_progress', 'resolved', 'escalated'])],
            'problems.*.resolution_desc' => ['nullable', 'string'],
            'problems.*.resolved_at' => ['nullable', 'date_format:Y-m-d H:i:s'],
        ])->validate();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, int|string>
     */
    private function persistReport(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $inspection = Inspection::create([
                'asset_id' => $data['asset_id'],
                'inspector_id' => $data['inspector_id'],
                'inspected_at' => $data['inspected_at'],
                'type' => $data['type'],
                'notes' => $data['notes'] ?? null,
                'review_status' => $data['review_status'] ?? 'pending_review',
                'reviewed_by' => $data['reviewed_by'] ?? null,
                'reviewed_at' => $data['reviewed_at'] ?? null,
            ]);

            foreach ($data['items'] ?? [] as $item) {
                $inspection->itemChecks()->create([
                    'component_name' => $item['component_name'],
                    'condition_code' => $item['condition_code'],
                    'remark' => $item['remark'] ?? null,
                ]);
            }

            foreach ($data['problems'] ?? [] as $problemPayload) {
                $problemCode = $problemPayload['problem_code'] ?? 'PRB-'.str_pad((string) ($inspection->problems()->count() + 1), 4, '0');

                Problem::create([
                    'problem_code' => $problemCode,
                    'inspection_id' => $inspection->id,
                    'location_id' => $problemPayload['location_id'],
                    'category' => $problemPayload['category'],
                    'severity' => $problemPayload['severity'],
                    'description' => $problemPayload['description'],
                    'reported_by' => $problemPayload['reported_by'],
                    'assigned_to' => $problemPayload['assigned_to'] ?? null,
                    'status' => $problemPayload['status'],
                    'resolution_desc' => $problemPayload['resolution_desc'] ?? null,
                    'resolved_at' => $problemPayload['resolved_at'] ?? null,
                ]);
            }

            return [
                'message' => 'Inspection report imported successfully.',
                'inspection_id' => $inspection->id,
                'items' => count($data['items'] ?? []),
                'problems' => count($data['problems'] ?? []),
            ];
        });
    }

    private function removeImportDirectory(string $directory): void
    {
        foreach (glob($directory.'/*') ?: [] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        rmdir($directory);
    }

    public function pdf(Inspection $inspection): Response
    {
        $inspection->load(['asset.location', 'inspector', 'itemChecks', 'problems']);

        $lines = [
            'OASIS INSPECTION REPORT',
            'Report: REP-'.str_pad((string) $inspection->id, 4, '0', STR_PAD_LEFT),
            'Facility: '.$inspection->asset->name,
            'Location: '.$inspection->asset->location->name,
            'Inspector: '.$inspection->inspector->name,
            'Date: '.$inspection->inspected_at->format('Y-m-d H:i'),
            'Type: '.Str::headline($inspection->type),
            'Review status: '.Str::headline($inspection->review_status),
            'Findings: '.$inspection->problems->count(),
        ];

        foreach ($inspection->itemChecks as $check) {
            $lines[] = 'Check: '.$check->component_name.' ['.$check->condition_code.'] '.($check->remark ?? '');
        }

        foreach ($inspection->problems as $problem) {
            $lines[] = 'Problem: '.$problem->severity.' - '.$problem->description;
        }

        return response($this->buildPdf($lines), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="report-'.$inspection->id.'.pdf"',
        ]);
    }

    /**
     * Build a small dependency-free PDF for report downloads.
     *
     * @param  array<int, string>  $lines
     */
    private function buildPdf(array $lines): string
    {
        $content = "BT\n/F1 10 Tf\n50 790 Td\n";

        foreach (array_slice($lines, 0, 42) as $index => $line) {
            $content .= '('.$this->escapePdfText($line).") Tj\n";

            if ($index < count($lines) - 1) {
                $content .= "0 -16 Td\n";
            }
        }

        $content .= 'ET';
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Length '.strlen($content)." >>\nstream\n".$content."\nendstream",
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $number => $object) {
            $offsets[$number + 1] = strlen($pdf);
            $pdf .= ($number + 1)." 0 obj\n".$object."\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";

        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf."trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n".$xrefOffset."\n%%EOF";
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
