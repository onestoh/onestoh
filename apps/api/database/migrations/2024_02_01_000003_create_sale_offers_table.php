<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sale_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('asking_price', 12, 2);
            $table->decimal('offer_price', 12, 2)->nullable();
            $table->decimal('agreed_price', 12, 2)->nullable();
            $table->decimal('reservation_fee', 12, 2)->nullable();
            $table->enum('status', ['enquiry', 'negotiating', 'price_agreed', 'reservation_paid', 'balance_due', 'completed', 'cancelled', 'expired']);
            $table->date('payment_deadline')->nullable();
            $table->string('agreement_pdf_path')->nullable();
            $table->timestamp('agreement_signed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['asset_id', 'status']);
            $table->index(['buyer_id', 'status']);
        });

        Schema::create('test_drive_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->enum('status', ['requested', 'confirmed', 'completed', 'cancelled', 'no_show']);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->boolean('deposit_returned')->default(false);
            $table->text('inspection_notes')->nullable();
            $table->json('inspection_photos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_drive_bookings');
        Schema::dropIfExists('sale_offers');
    }
};
