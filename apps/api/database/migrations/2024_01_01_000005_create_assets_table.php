<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yard_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->enum('category', [
                'passenger_car', 'suv_4x4', 'van_minibus', 'pickup_truck',
                'heavy_truck', 'excavator', 'tractor_farm', 'crane_lift',
                'generator', 'compactor_roller', 'motorcycle_tuktuk', 'special_equipment'
            ]);
            $table->string('sub_type', 60)->nullable();
            $table->string('make', 60);
            $table->string('model', 80);
            $table->unsignedSmallInteger('year');
            $table->string('registration_plate_encrypted'); // AES encrypted
            $table->string('vin_encrypted')->nullable();
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid', 'lpg'])->nullable();
            $table->enum('transmission', ['automatic', 'manual', 'cvt'])->nullable();
            $table->unsignedTinyInteger('seats')->nullable();
            $table->string('engine_size', 30)->nullable();
            $table->string('load_capacity', 50)->nullable();
            $table->enum('status', ['active', 'under_maintenance', 'off_road', 'for_sale', 'retired'])->default('active');
            $table->enum('listing_mode', ['rent_only', 'sale_only', 'rent_and_sale'])->default('rent_only');
            $table->boolean('is_self_drive_enabled')->default(true);
            $table->boolean('is_chauffeur_enabled')->default(false);
            $table->boolean('is_delivery_enabled')->default(false);
            $table->decimal('delivery_fee_per_km', 8, 2)->nullable();
            // Rates
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->unsignedTinyInteger('minimum_hours')->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->decimal('weekly_rate', 10, 2)->nullable();
            $table->decimal('monthly_rate', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->unsignedInteger('mileage_cap_per_day')->nullable();
            $table->decimal('mileage_overage_per_km', 8, 2)->nullable();
            $table->decimal('overtime_per_hour', 8, 2)->nullable();
            // Location
            $table->string('pickup_county');
            $table->string('pickup_area');
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            // Features
            $table->json('features')->nullable(); // {ac: true, gps: true, ...}
            $table->text('usage_rules')->nullable();
            $table->text('description')->nullable();
            // Sale fields
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->text('sale_description')->nullable();
            // Admin
            $table->boolean('is_published')->default(false);
            $table->timestamp('admin_approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->unsignedInteger('total_completed_rentals')->default(0);
            // Photo hashes for duplicate detection
            $table->json('photo_hashes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['category', 'pickup_county', 'is_published', 'status']);
            $table->index(['owner_id', 'status']);
            $table->index(['listing_mode', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
