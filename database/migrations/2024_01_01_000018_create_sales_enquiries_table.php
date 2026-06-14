<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sales_enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('broker_id')->nullable();
            $table->decimal('offered_price', 12, 2)->nullable();
            $table->decimal('owner_counter', 12, 2)->nullable();
            $table->decimal('agreed_price', 12, 2)->nullable();
            $table->decimal('reservation_fee', 12, 2)->nullable();
            $table->enum('status', ['open','negotiating','reserved','completed','expired','cancelled'])->default('open');
            $table->timestamp('reservation_expires_at')->nullable();
            $table->timestamps();
            $table->foreign('client_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('broker_id')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sales_enquiries');
    }
};
