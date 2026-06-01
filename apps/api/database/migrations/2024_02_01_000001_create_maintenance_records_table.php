<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('logged_by')->constrained('users')->cascadeOnDelete();
            $table->enum('service_type', ['oil_change', 'tyre_rotation', 'brake_service', 'engine_service', 'body_repair', 'electrical', 'inspection', 'insurance_renewal', 'other']);
            $table->text('description')->nullable();
            $table->string('service_centre')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->unsignedInteger('odometer_at_service')->nullable();
            $table->unsignedInteger('next_service_km')->nullable();
            $table->date('next_service_date')->nullable();
            $table->date('service_date');
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->index(['asset_id', 'service_date']);
        });

        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('interval_type', ['km', 'days', 'months']);
            $table->unsignedInteger('interval_value');
            $table->date('last_done_at')->nullable();
            $table->date('next_due_at')->nullable();
            $table->unsignedInteger('alert_days_before')->default(14);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
        Schema::dropIfExists('maintenance_records');
    }
};
