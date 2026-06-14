<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('yard_id')->nullable();
            $table->foreignId('category_id')->constrained('asset_categories')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('description');
            $table->string('asset_type', 100);
            $table->string('make', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->integer('year')->nullable();
            $table->string('registration_plate', 20)->nullable();
            $table->string('engine_size', 50)->nullable();
            $table->string('fuel_type', 50)->nullable();
            $table->string('transmission', 50)->nullable();
            $table->integer('seats')->nullable();
            $table->string('load_capacity', 100)->nullable();
            $table->enum('listing_mode', ['rental','sale','both'])->default('rental');
            $table->enum('status', ['draft','pending','active','suspended','sold'])->default('draft');
            $table->decimal('hourly_rate', 12, 2)->nullable();
            $table->decimal('daily_rate', 12, 2)->nullable();
            $table->decimal('weekly_rate', 12, 2)->nullable();
            $table->decimal('monthly_rate', 12, 2)->nullable();
            $table->integer('min_hourly')->default(2);
            $table->decimal('security_deposit', 12, 2)->default(0);
            $table->decimal('driver_surcharge_daily', 12, 2)->nullable();
            $table->decimal('delivery_fee_per_km', 8, 2)->nullable();
            $table->integer('mileage_cap')->nullable();
            $table->decimal('overtime_charge', 8, 2)->nullable();
            $table->enum('drive_mode', ['self_drive','chauffeur','both'])->default('both');
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('county', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('area', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('features')->nullable();
            $table->string('insurance_cert', 255)->nullable();
            $table->date('ntsa_sticker_date')->nullable();
            $table->string('vin_number', 100)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('yard_id')->references('id')->on('yards')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('listings');
    }
};
