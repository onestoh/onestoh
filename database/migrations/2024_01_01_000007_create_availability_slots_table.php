<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->tinyInteger('hour_from')->nullable();
            $table->tinyInteger('hour_to')->nullable();
            $table->enum('status', ['available','booked','pending','owner_blocked'])->default('available');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('availability_slots');
    }
};
