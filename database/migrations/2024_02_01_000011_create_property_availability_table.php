<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->date('blocked_date');
            $table->enum('reason', ['booked', 'owner_blocked', 'maintenance'])->default('booked');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->timestamps();
            $table->unique(['property_id', 'blocked_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_availability');
    }
};
