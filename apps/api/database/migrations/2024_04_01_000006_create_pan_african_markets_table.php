<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_configs', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->unique();
            $table->string('country_name');
            $table->string('currency_code', 3);
            $table->string('currency_symbol', 5);
            $table->string('timezone', 40);
            $table->string('phone_prefix', 6);
            $table->string('phone_format', 30); // regex pattern
            $table->json('payment_gateways'); // available gateways per country
            $table->json('id_document_types'); // accepted ID types
            $table->string('language_code', 5)->default('en');
            $table->boolean('is_launched')->default(false);
            $table->boolean('is_beta')->default(false);
            $table->date('launch_date')->nullable();
            $table->json('regulatory_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('paystack_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 80)->unique();
            $table->string('access_code')->nullable();
            $table->decimal('amount_kobo', 14, 0); // Paystack stores in kobo/pesewas
            $table->string('currency', 3)->default('NGN');
            $table->enum('status', ['pending', 'success', 'failed', 'abandoned'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['reference']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paystack_transactions');
        Schema::dropIfExists('market_configs');
    }
};
