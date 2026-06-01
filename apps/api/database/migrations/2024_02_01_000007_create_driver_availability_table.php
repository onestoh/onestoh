<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['available', 'unavailable', 'on_leave', 'assigned']);
            $table->uuid('booking_id')->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['driver_id', 'date']);
        });

        Schema::create('yard_driver_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('yard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamp('assigned_at');
            $table->timestamp('removed_at')->nullable();
            $table->timestamps();
            $table->unique(['driver_id', 'yard_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yard_driver_assignments');
        Schema::dropIfExists('driver_availability');
    }
};
