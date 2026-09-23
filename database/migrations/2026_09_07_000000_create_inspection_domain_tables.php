<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('badge_id', 30)->nullable()->unique()->after('name');
            $table->string('phone', 30)->nullable()->after('email');
            $table->enum('role', ['security', 'danru', 'chief_security', 'admin'])->default('security')->after('password');
            $table->enum('shift', ['day', 'night'])->nullable()->after('role');
            $table->unsignedBigInteger('assigned_location_id')->nullable()->after('shift');
            $table->enum('employment_status', ['active', 'on_leave', 'inactive'])->default('active')->after('assigned_location_id');
        });

        Schema::create('asset_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('name', 150);
            $table->enum('type', ['apartment', 'tower', 'floor', 'area']);
            $table->string('building_code', 30)->nullable();
            $table->timestamps();
            $table->index('type');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreign('assigned_location_id')->references('id')->on('locations')->nullOnDelete();
        });

        Schema::create('assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained('asset_categories')->restrictOnDelete();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->date('install_date')->nullable();
            $table->date('service_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('condition_status', ['good', 'fair', 'poor'])->nullable();
            $table->enum('status', ['operational', 'needs_service', 'under_repair'])->default('operational');
            $table->string('qr_code', 100)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['status', 'condition_status']);
        });

        Schema::create('inspection_schedules', function (Blueprint $table): void {
            $table->id();
            $table->string('schedule_code', 30)->unique();
            $table->foreignId('officer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('patrol_zone_id')->constrained('locations')->restrictOnDelete();
            $table->date('scheduled_date');
            $table->time('shift_start');
            $table->time('shift_end');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'overdue', 'missed'])->default('scheduled');
            $table->timestamps();
            $table->index(['scheduled_date', 'status']);
        });

        Schema::create('inspections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
            $table->foreignId('inspector_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('inspected_at');
            $table->enum('type', ['routine', 'emergency', 'follow_up'])->default('routine');
            $table->text('notes')->nullable();
            $table->enum('review_status', ['pending_review', 'approved', 'rejected'])->default('pending_review');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['inspected_at', 'review_status']);
        });

        Schema::create('inspection_item_checks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->string('component_name', 50);
            $table->enum('condition_code', ['V', 'X', 'R']);
            $table->string('remark')->nullable();
            $table->timestamps();
        });

        Schema::create('inspection_photos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->string('file_path');
            $table->dateTime('taken_at');
            $table->timestamps();
        });

        Schema::create('problems', function (Blueprint $table): void {
            $table->id();
            $table->string('problem_code', 30)->unique();
            $table->foreignId('inspection_id')->nullable()->constrained('inspections')->nullOnDelete();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->enum('category', ['electrical', 'plumbing', 'fire_safety', 'hvac', 'structural', 'general'])->default('general');
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->text('description');
            $table->foreignId('reported_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['open', 'in_progress', 'resolved', 'escalated'])->default('open');
            $table->text('resolution_desc')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['location_id', 'status', 'severity', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problems');
        Schema::dropIfExists('inspection_photos');
        Schema::dropIfExists('inspection_item_checks');
        Schema::dropIfExists('inspections');
        Schema::dropIfExists('inspection_schedules');
        Schema::dropIfExists('assets');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['assigned_location_id']);
        });
        Schema::dropIfExists('locations');
        Schema::dropIfExists('asset_categories');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['badge_id', 'phone', 'role', 'shift', 'assigned_location_id', 'employment_status']);
        });
    }
};
