<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('heavy_equipment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('machine_class', 60)->nullable();
            $table->string('operating_weight', 50)->nullable();
            $table->string('bucket_blade_capacity', 50)->nullable();
            $table->decimal('fuel_consumption_per_hour', 8, 2)->nullable();
            $table->enum('transport_method', ['self_propelled', 'flatbed', 'low_loader']);
            $table->decimal('mobilisation_cost', 10, 2)->default(0);
            $table->decimal('demobilisation_cost', 10, 2)->default(0);
            $table->unsignedSmallInteger('minimum_hire_days')->default(1);
            $table->unsignedTinyInteger('max_daily_hours')->default(8);
            $table->text('site_access_requirements')->nullable();
            $table->enum('operator_cert_required', ['none', 'level_1', 'level_2', 'osha']);
            $table->enum('fuel_provisioning', ['client_provided', 'owner_provided', 'both']);
            $table->boolean('owner_review_required')->default(true);
            $table->decimal('early_termination_fee_pct', 5, 2)->default(50.00);
            $table->string('roadworthiness_cert_path')->nullable();
            $table->date('roadworthiness_expires_at')->nullable();
            $table->timestamps();
        });

        // Add heavy_equipment fields to bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('project_description')->nullable()->after('client_notes');
            $table->string('site_location_address')->nullable()->after('project_description');
            $table->decimal('site_latitude', 10, 7)->nullable()->after('site_location_address');
            $table->decimal('site_longitude', 10, 7)->nullable()->after('site_latitude');
            $table->timestamp('mobilisation_at')->nullable()->after('site_longitude');
            $table->unsignedTinyInteger('agreed_daily_hours')->nullable()->after('mobilisation_at');
            $table->enum('fuel_arrangement', ['client', 'owner'])->nullable()->after('agreed_daily_hours');
            $table->decimal('mobilisation_cost', 10, 2)->default(0)->after('fuel_arrangement');
            $table->timestamp('owner_reviewed_at')->nullable()->after('mobilisation_cost');
            $table->boolean('owner_approved')->nullable()->after('owner_reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heavy_equipment_details');
    }
};
