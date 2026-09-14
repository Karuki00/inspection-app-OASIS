<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Inspection;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use ZipArchive;

class InspectionReportImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_import_report_json_and_persist_related_records(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'employment_status' => 'active',
        ]);

        $inspector = User::factory()->create([
            'name' => 'Inspector User',
            'email' => 'inspector@example.com',
            'role' => 'security',
            'employment_status' => 'active',
        ]);

        $category = AssetCategory::create([
            'name' => 'CCTV Camera',
            'description' => 'Surveillance equipment',
        ]);

        $location = Location::create([
            'name' => 'Main Gate',
            'type' => 'area',
            'building_code' => 'MG-01',
        ]);

        $asset = Asset::create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'code' => 'ASSET-900',
            'name' => 'Main Gate Camera',
            'condition_status' => 'good',
            'status' => 'operational',
            'qr_code' => 'QR-900',
            'is_active' => true,
        ]);

        $payload = [
            'asset_id' => $asset->id,
            'inspector_id' => $inspector->id,
            'inspected_at' => '2026-09-10 08:00:00',
            'type' => 'routine',
            'notes' => 'Routine gate inspection.',
            'review_status' => 'pending_review',
            'items' => [
                ['component_name' => 'Gate Camera', 'condition_code' => 'V', 'remark' => 'Camera is clear'],
                ['component_name' => 'Gate Lock', 'condition_code' => 'X', 'remark' => 'Lock should be checked'],
            ],
            'problems' => [
                [
                    'problem_code' => 'PRB-1001',
                    'location_id' => $location->id,
                    'category' => 'electrical',
                    'severity' => 'high',
                    'description' => 'Power supply needs review.',
                    'reported_by' => $inspector->id,
                    'assigned_to' => $inspector->id,
                    'status' => 'open',
                ],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson(route('inspection-reports.import'), $payload);

        $response->assertOk();
        $this->assertDatabaseHas('inspections', [
            'asset_id' => $asset->id,
            'inspector_id' => $inspector->id,
            'type' => 'routine',
            'review_status' => 'pending_review',
        ]);

        $inspection = Inspection::query()->where('asset_id', $asset->id)->firstOrFail();

        $this->assertDatabaseHas('inspection_item_checks', [
            'inspection_id' => $inspection->id,
            'component_name' => 'Gate Camera',
            'condition_code' => 'V',
        ]);

        $this->assertDatabaseHas('problems', [
            'inspection_id' => $inspection->id,
            'problem_code' => 'PRB-1001',
            'location_id' => $location->id,
            'category' => 'electrical',
            'severity' => 'high',
            'status' => 'open',
        ]);

        $this->assertDatabaseCount('problems', 1);
    }

    public function test_admin_can_download_a_report_as_a_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inspector = User::factory()->create(['role' => 'security', 'password' => null]);
        $category = AssetCategory::create(['name' => 'CCTV Camera']);
        $location = Location::create(['name' => 'Main Gate', 'type' => 'area']);
        $asset = Asset::create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'code' => 'ASSET-901',
            'name' => 'Main Gate Camera',
            'status' => 'operational',
            'is_active' => true,
        ]);
        $inspection = Inspection::create([
            'asset_id' => $asset->id,
            'inspector_id' => $inspector->id,
            'inspected_at' => '2026-09-10 08:00:00',
            'type' => 'routine',
        ]);

        $response = $this->actingAs($admin)->get(route('inspection-reports.pdf', $inspection));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'attachment; filename="report-'.$inspection->id.'.pdf"');
        $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
    }

    public function test_admin_can_import_a_report_json_from_a_zip(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inspector = User::factory()->create(['role' => 'security', 'password' => null]);
        $category = AssetCategory::create(['name' => 'Hydrant']);
        $location = Location::create(['name' => 'Lobby', 'type' => 'area']);
        $asset = Asset::create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'code' => 'ZIP-001',
            'name' => 'Lobby Hydrant',
            'status' => 'operational',
            'is_active' => true,
        ]);
        $jsonPath = storage_path('app/report-test.json');
        $zipPath = storage_path('app/report-test.zip');
        file_put_contents($jsonPath, json_encode([
            'asset_id' => $asset->id,
            'inspector_id' => $inspector->id,
            'inspected_at' => '2026-09-14 08:00:00',
            'type' => 'routine',
        ], JSON_THROW_ON_ERROR));
        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE);
        $zip->addFile($jsonPath, 'inspection.json');
        $zip->close();

        try {
            $response = $this->actingAs($admin)->post(route('inspection-reports.import-zip'), [
                'report_zip' => UploadedFile::fake()->createWithContent('report.zip', file_get_contents($zipPath)),
            ]);

            $response->assertRedirect()->assertSessionHas('status');
            $this->assertDatabaseHas('inspections', ['asset_id' => $asset->id, 'inspector_id' => $inspector->id]);
        } finally {
            @unlink($jsonPath);
            @unlink($zipPath);
        }
    }
}
