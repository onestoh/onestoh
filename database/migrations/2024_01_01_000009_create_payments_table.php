<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('payment_method', ['mpesa_stk','mpesa_paybill','card','wallet','bank_transfer','flutterwave']);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 5)->default('KES');
            $table->enum('status', ['pending','completed','failed','reversed']);
            $table->string('gateway_ref', 100)->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('mpesa_receipt', 50)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
