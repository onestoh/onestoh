<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('broker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('referral_code', 12)->nullable();
            $table->enum('rental_type', ['self_drive', 'chauffeur'])->default('self_drive');
            $table->enum('duration_type', ['hourly', 'daily', 'weekly', 'monthly']);
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->timestamp('actual_end_at')->nullable();
            $table->enum('status', [
                'pending_payment', 'payment_processing', 'confirmed',
                'owner_notified', 'client_prepared', 'active',
                'completed', 'closed', 'cancelled_by_client',
                'cancelled_by_admin', 'cancelled_by_system', 'disputed'
            ])->default('pending_payment');
            // Amounts
            $table->string('currency', 3)->default('KES');
            $table->decimal('base_amount', 12, 2);
            $table->decimal('security_deposit_amount', 12, 2)->default(0);
            $table->decimal('driver_surcharge', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('insurance_fee', 12, 2)->default(0);
            $table->decimal('platform_fee', 12, 2)->default(0);
            $table->decimal('broker_commission', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            // Pickup/delivery
            $table->string('pickup_address')->nullable();
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->string('delivery_address')->nullable();
            $table->decimal('delivery_latitude', 10, 7)->nullable();
            $table->decimal('delivery_longitude', 10, 7)->nullable();
            // Rental details
            $table->json('pre_rental_photos')->nullable();
            $table->json('post_rental_photos')->nullable();
            $table->unsignedInteger('start_odometer')->nullable();
            $table->unsignedInteger('end_odometer')->nullable();
            // Cancellation
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            // Agreement
            $table->string('agreement_pdf_path')->nullable();
            $table->timestamp('agreement_signed_at')->nullable();
            // Notes
            $table->text('client_notes')->nullable();
            $table->text('owner_notes')->nullable();
            $table->timestamps();
            $table->index(['asset_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['broker_id']);
            $table->index(['status', 'end_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
