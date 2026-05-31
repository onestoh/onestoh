<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('hotel_rooms')->nullOnDelete();
            $table->foreignId('guest_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('host_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['inspection', 'airbnb', 'hotel'])->default('airbnb');
            $table->date('check_in');
            $table->date('check_out');
            $table->tinyInteger('guests_count')->default(1);
            $table->tinyInteger('nights')->nullable();
            $table->decimal('total_price', 12, 2);
            $table->decimal('base_price_per_night', 10, 2);
            $table->decimal('cleaning_fee', 8, 2)->default(0);
            $table->decimal('service_fee', 8, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'paid', 'checked_in', 'checked_out', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_method', ['mpesa', 'card', 'bank'])->nullable();
            $table->string('payment_ref')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('host_notified_at')->nullable();
            $table->boolean('auto_confirmed')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
