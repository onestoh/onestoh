<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('gateway', ['mpesa_stk', 'mpesa_paybill', 'card_dpo', 'flutterwave', 'wallet', 'bank_transfer']);
            $table->enum('type', ['rental', 'deposit', 'insurance', 'payout', 'refund', 'commission']);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('KES');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('gateway_reference')->nullable(); // M-Pesa transaction ID etc
            $table->string('gateway_checkout_id')->nullable(); // STK push checkout ID
            $table->string('phone_number', 20)->nullable(); // for M-Pesa
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('receipt_pdf_path')->nullable();
            $table->string('idempotency_key', 64)->unique();
            $table->timestamps();
            $table->index(['booking_id', 'type', 'status']);
            $table->index(['gateway_reference']);
            $table->index(['gateway_checkout_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
