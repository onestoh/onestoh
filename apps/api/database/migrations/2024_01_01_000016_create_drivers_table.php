<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('primary_yard_id')->nullable()->constrained('yards')->nullOnDelete();
            $table->enum('licence_class', ['private', 'psv_a', 'psv_b', 'heavy_vehicle', 'operator_level1', 'operator_level2', 'osha_certified'])->nullable();
            $table->string('licence_number', 30)->nullable();
            $table->date('licence_expiry')->nullable();
            $table->boolean('is_freelance')->default(false);
            $table->boolean('is_available')->default(true);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_trips')->default(0);
            $table->unsignedInteger('incident_free_trips')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
