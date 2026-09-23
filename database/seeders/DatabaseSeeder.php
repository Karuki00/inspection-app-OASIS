<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\Location;
use App\Models\Problem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Box Hydrant', 'description' => 'Fire hydrant box with hose and valve'],
            ['name' => 'Tangga Darurat', 'description' => 'Emergency stairway and exit equipment'],
            ['name' => 'APAR', 'description' => 'Standalone fire extinguisher'],
            ['name' => 'CCTV Camera', 'description' => 'Security camera unit'],
            ['name' => 'Elevator', 'description' => 'Passenger or service lift'],
            ['name' => 'HVAC', 'description' => 'Heating, ventilation, and air-conditioning equipment'],
            ['name' => 'Electrical Panel', 'description' => 'Electrical distribution panel'],
            ['name' => 'Pump', 'description' => 'Water or fire pump'],
        ])->mapWithKeys(function (array $category): array {
            $record = AssetCategory::updateOrCreate(['name' => $category['name']], $category);

            return [$category['name'] => $record];
        });

        $mainLocation = Location::updateOrCreate(
            ['name' => 'Apartemen OASIS Mitra Sarana'],
            ['type' => 'apartment', 'building_code' => 'OASIS']
        );

        $locations = collect([
            ['name' => 'Tower A', 'type' => 'tower', 'building_code' => 'A'],
            ['name' => 'Tower B', 'type' => 'tower', 'building_code' => 'B'],
            ['name' => 'Main Lobby Entrance', 'type' => 'area', 'building_code' => 'LOBBY'],
            ['name' => 'Basement Parking B1-B2', 'type' => 'area', 'building_code' => 'PARKING'],
            ['name' => 'Level 5 Recreation & Pool', 'type' => 'area', 'building_code' => 'REC-05'],
            ['name' => 'Rooftop & Sky Lounge', 'type' => 'area', 'building_code' => 'ROOF'],
            ['name' => 'Building Perimeter & Gates', 'type' => 'area', 'building_code' => 'SECURITY'],
        ])->mapWithKeys(function (array $location) use ($mainLocation): array {
            $record = Location::updateOrCreate(
                ['name' => $location['name']],
                [...$location, 'parent_location_id' => $mainLocation->id]
            );

            return [$location['name'] => $record];
        });

        $admin = User::updateOrCreate(
            ['email' => 'admin@oasis.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'employment_status' => 'active',
            ]
        );

        $personnel = collect([
            ['name' => 'Bambang Wijaya', 'badge_id' => 'SG-1024', 'email' => 'bambang@oasis.com', 'phone' => '+62 812-3456-7890', 'role' => 'security', 'shift' => 'day', 'assigned_location_id' => $locations['Main Lobby Entrance']->id],
            ['name' => 'Rian Hidayat', 'badge_id' => 'SG-1042', 'email' => 'rian@oasis.com', 'phone' => '+62 813-9012-3456', 'role' => 'security', 'shift' => 'night', 'assigned_location_id' => $locations['Tower A']->id],
            ['name' => 'Siti Rahma', 'badge_id' => 'SG-1087', 'email' => 'siti@oasis.com', 'phone' => '+62 811-5678-9012', 'role' => 'security', 'shift' => 'day', 'employment_status' => 'on_leave', 'assigned_location_id' => $locations['Level 5 Recreation & Pool']->id],
            ['name' => 'Dwi Setyo', 'badge_id' => 'SG-1101', 'email' => 'dwi@oasis.com', 'phone' => '+62 812-1111-2222', 'role' => 'chief_security', 'shift' => 'night', 'assigned_location_id' => $locations['Basement Parking B1-B2']->id],
        ])->mapWithKeys(function (array $person): array {
            $record = User::updateOrCreate(
                ['email' => $person['email']],
                [...$person, 'password' => null]
            );

            return [$person['name'] => $record];
        });

        $assets = collect([
            ['code' => 'HB-101', 'name' => 'Lobby Box Hydrant', 'category' => 'Box Hydrant', 'location' => 'Main Lobby Entrance', 'condition_status' => 'good', 'status' => 'operational'],
            ['code' => 'HB-102', 'name' => 'Basement Box Hydrant', 'category' => 'Box Hydrant', 'location' => 'Basement Parking B1-B2', 'condition_status' => 'poor', 'status' => 'needs_service'],
            ['code' => 'EL-201', 'name' => 'Passenger Lift B', 'category' => 'Elevator', 'location' => 'Tower A', 'condition_status' => 'good', 'status' => 'operational'],
            ['code' => 'HV-301', 'name' => 'Sky Lounge HVAC Condenser', 'category' => 'HVAC', 'location' => 'Rooftop & Sky Lounge', 'condition_status' => 'fair', 'status' => 'under_repair'],
            ['code' => 'CC-401', 'name' => 'Main Entrance CCTV', 'category' => 'CCTV Camera', 'location' => 'Main Lobby Entrance', 'condition_status' => 'good', 'status' => 'operational'],
            ['code' => 'AP-501', 'name' => 'Pool Deck APAR', 'category' => 'APAR', 'location' => 'Level 5 Recreation & Pool', 'condition_status' => 'good', 'status' => 'operational'],
        ])->mapWithKeys(function (array $asset) use ($categories, $locations): array {
            $record = Asset::updateOrCreate(
                ['code' => $asset['code']],
                [
                    'category_id' => $categories[$asset['category']]->id,
                    'location_id' => $locations[$asset['location']]->id,
                    'name' => $asset['name'],
                    'condition_status' => $asset['condition_status'],
                    'status' => $asset['status'],
                    'is_active' => true,
                ]
            );

            return [$asset['code'] => $record];
        });

        $scheduleData = [
            ['schedule_code' => 'SCH-2401', 'officer' => 'Bambang Wijaya', 'zone' => 'Main Lobby Entrance', 'scheduled_date' => '2026-09-08', 'shift_start' => '07:00', 'shift_end' => '15:00', 'status' => 'scheduled'],
            ['schedule_code' => 'SCH-2402', 'officer' => 'Rian Hidayat', 'zone' => 'Basement Parking B1-B2', 'scheduled_date' => '2026-09-07', 'shift_start' => '15:00', 'shift_end' => '23:00', 'status' => 'overdue'],
            ['schedule_code' => 'SCH-2403', 'officer' => 'Siti Rahma', 'zone' => 'Rooftop & Sky Lounge', 'scheduled_date' => '2026-09-09', 'shift_start' => '07:00', 'shift_end' => '15:00', 'status' => 'scheduled'],
            ['schedule_code' => 'SCH-2404', 'officer' => 'Dwi Setyo', 'zone' => 'Level 5 Recreation & Pool', 'scheduled_date' => '2026-09-09', 'shift_start' => '15:00', 'shift_end' => '23:00', 'status' => 'in_progress'],
        ];

        foreach ($scheduleData as $schedule) {
            InspectionSchedule::updateOrCreate(
                ['schedule_code' => $schedule['schedule_code']],
                [
                    'officer_id' => $personnel[$schedule['officer']]->id,
                    'patrol_zone_id' => $locations[$schedule['zone']]->id,
                    'scheduled_date' => $schedule['scheduled_date'],
                    'shift_start' => $schedule['shift_start'],
                    'shift_end' => $schedule['shift_end'],
                    'status' => $schedule['status'],
                ]
            );
        }

        $inspection = Inspection::updateOrCreate(
            ['asset_id' => $assets['HB-101']->id, 'inspected_at' => '2026-09-06 09:00:00'],
            ['inspector_id' => $personnel['Bambang Wijaya']->id, 'type' => 'routine', 'notes' => 'Routine hydrant inspection completed.', 'review_status' => 'approved', 'reviewed_by' => $admin->id, 'reviewed_at' => '2026-09-06 12:00:00']
        );

        Problem::updateOrCreate(
            ['problem_code' => 'PRB-2026-001'],
            ['inspection_id' => $inspection->id, 'location_id' => $locations['Basement Parking B1-B2']->id, 'category' => 'electrical', 'severity' => 'critical', 'description' => 'Sump pump overheating warning.', 'reported_by' => $personnel['Rian Hidayat']->id, 'assigned_to' => $personnel['Dwi Setyo']->id, 'status' => 'open']
        );

        Problem::updateOrCreate(
            ['problem_code' => 'PRB-2026-002'],
            ['location_id' => $locations['Level 5 Recreation & Pool']->id, 'category' => 'plumbing', 'severity' => 'high', 'description' => 'Pool filtration pressure requires service.', 'reported_by' => $personnel['Siti Rahma']->id, 'assigned_to' => $personnel['Bambang Wijaya']->id, 'status' => 'in_progress']
        );

        Problem::updateOrCreate(
            ['problem_code' => 'PRB-2026-003'],
            ['location_id' => $locations['Tower A']->id, 'category' => 'fire_safety', 'severity' => 'medium', 'description' => 'Emergency lighting battery needs replacement.', 'reported_by' => $personnel['Bambang Wijaya']->id, 'assigned_to' => $personnel['Rian Hidayat']->id, 'status' => 'resolved', 'resolution_desc' => 'Battery replaced and retested.', 'resolved_at' => '2026-09-05 15:00:00']
        );
    }
}
