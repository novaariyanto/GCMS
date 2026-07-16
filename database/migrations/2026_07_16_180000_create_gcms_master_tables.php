<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regional_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('app_name');
            $table->string('government_name');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('theme_color', 20)->default('#0B5ED7');
            $table->text('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('head_of_region')->nullable();
            $table->string('regional_secretary')->nullable();
            $table->text('dashboard_greeting')->nullable();
            $table->json('social_media')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('provinces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('regencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('province_id')->constrained()->cascadeOnDelete();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->enum('type', ['kabupaten', 'kota'])->default('kabupaten');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['province_id', 'is_active']);
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('regency_id')->constrained()->cascadeOnDelete();
            $table->string('code', 15)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['regency_id', 'is_active']);
        });

        Schema::create('villages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('district_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->enum('type', ['desa', 'kelurahan'])->default('desa');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['district_id', 'is_active']);
        });

        Schema::create('opds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('short_name', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('divisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('opd_id')->constrained('opds')->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['opd_id', 'code']);
        });

        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('opd_id')->constrained('opds')->cascadeOnDelete();
            $table->foreignUuid('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->string('code', 30);
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['opd_id', 'code']);
            $table->index(['opd_id', 'is_active']);
        });

        Schema::create('complaint_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('complaint_sub_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('complaint_categories')->cascadeOnDelete();
            $table->foreignUuid('default_opd_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->string('code', 30);
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['category_id', 'code']);
        });

        Schema::create('priorities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('color', 20)->default('#6c757d');
            $table->integer('level')->default(1);
            $table->integer('sla_hours')->default(72);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('slas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->foreignUuid('priority_id')->nullable()->constrained('priorities')->nullOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained('complaint_categories')->nullOnDelete();
            $table->integer('response_hours')->default(24);
            $table->integer('resolution_hours')->default(72);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('complaint_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('color', 20)->default('#6c757d');
            $table->boolean('is_final')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date')->unique();
            $table->string('name');
            $table->boolean('is_national')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('working_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedTinyInteger('day_of_week'); // 0=Sunday
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['day_of_week', 'start_time']);
        });

        Schema::create('attachment_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->json('allowed_extensions');
            $table->unsignedInteger('max_size_kb')->default(5120);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('notification_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        $tables = [
            'notification_media', 'attachment_types', 'working_hours', 'holidays',
            'complaint_statuses', 'slas', 'priorities', 'complaint_sub_categories',
            'complaint_categories', 'units', 'divisions', 'opds',
            'villages', 'districts', 'regencies', 'provinces', 'regional_settings',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
