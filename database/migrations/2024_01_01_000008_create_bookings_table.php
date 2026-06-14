<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->unsignedBigInteger('broker_id')->nullable();
            $table->string('booking_ref', 20)->unique();
            $table->enum('mode', ['self_drive','chauffeur']);
            $table->enum('duration_type', ['hourly','daily','weekly','monthly']);
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->text('pickup_location')->nullable();
            $table->text('delivery_address')->nullable();
            $table->decimal('base_amount', 12, 2);
            $table->decimal('security_deposit', 12, 2);
            $table->decimal('driver_surcharge', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('platform_fee', 12, 2)->default(0);
            $table->decimal('insurance_fee', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['pending_payment','confirmed','active','completed','cancelled','disputed'])->default('pending_payment');
            $table->timestamp('slot_hold_expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('pre_photos')->nullable();
            $table->json('post_photos')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->foreign('client_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('operator_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('broker_id')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};
