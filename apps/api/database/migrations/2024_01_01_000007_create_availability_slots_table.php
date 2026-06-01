<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedTinyInteger('hour')->nullable(); // null = full day
            $table->enum('status', ['available', 'pending', 'confirmed', 'owner_blocked'])->default('available');
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('hold_expires_at')->nullable();
            $table->timestamps();
            $table->unique(['asset_id', 'date', 'hour']);
            $table->index(['asset_id', 'date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_slots');
    }
};
