<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('broker_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('broker_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->enum('status', ['pending','credited','paid'])->default('pending');
            $table->timestamp('credited_at')->nullable();
            $table->timestamps();
            $table->foreign('broker_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('broker_commissions');
    }
};
