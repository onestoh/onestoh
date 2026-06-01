<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('telematics_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('device_id', 60)->unique();
            $table->string('device_type', 40)->default('gps_tracker'); // gps_tracker, obd2, canbus
            $table->string('sim_iccid', 30)->nullable();
            $table->string('firmware_version', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_ping_at')->nullable();
            $table->timestamps();
        });

        Schema::create('telematics_pings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('device_id', 60);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedSmallInteger('speed_kmh')->default(0);
            $table->unsignedSmallInteger('heading')->nullable(); // degrees 0-359
            $table->unsignedInteger('odometer_km')->nullable();
            $table->unsignedTinyInteger('fuel_level_pct')->nullable();
            $table->boolean('ignition_on')->default(false);
            $table->boolean('engine_on')->default(false);
            $table->decimal('battery_voltage', 5, 2)->nullable();
            $table->json('alerts')->nullable(); // speeding, geofence_breach, harsh_braking
            $table->timestamp('pinged_at');
            $table->index(['asset_id', 'pinged_at']);
            $table->index(['device_id', 'pinged_at']);
        });

        Schema::create('geofences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['circle', 'polygon']);
            $table->decimal('center_latitude', 10, 7)->nullable();
            $table->decimal('center_longitude', 10, 7)->nullable();
            $table->unsignedInteger('radius_meters')->nullable(); // for circle
            $table->json('polygon_coordinates')->nullable(); // for polygon
            $table->boolean('alert_on_entry')->default(true);
            $table->boolean('alert_on_exit')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('telematics_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->uuid('booking_id')->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->nullOnDelete();
            $table->decimal('start_latitude', 10, 7);
            $table->decimal('start_longitude', 10, 7);
            $table->decimal('end_latitude', 10, 7)->nullable();
            $table->decimal('end_longitude', 10, 7)->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('distance_km')->default(0);
            $table->unsignedSmallInteger('max_speed_kmh')->default(0);
            $table->unsignedSmallInteger('avg_speed_kmh')->default(0);
            $table->unsignedInteger('idle_seconds')->default(0);
            $table->unsignedSmallInteger('harsh_braking_events')->default(0);
            $table->unsignedSmallInteger('speeding_events')->default(0);
            $table->decimal('fuel_consumed_litres', 8, 2)->nullable();
            $table->unsignedTinyInteger('driver_score')->nullable(); // 0-100
            $table->json('route_polyline')->nullable(); // encoded polyline for map
            $table->timestamps();
            $table->index(['asset_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telematics_trips');
        Schema::dropIfExists('geofences');
        Schema::dropIfExists('telematics_pings');
        Schema::dropIfExists('telematics_devices');
    }
};
