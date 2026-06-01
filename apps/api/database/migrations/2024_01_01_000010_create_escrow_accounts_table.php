<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('escrow_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_id')->unique();
            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
            $table->decimal('total_collected', 12, 2)->default(0);
            $table->decimal('rental_fee', 12, 2)->default(0);
            $table->decimal('security_deposit', 12, 2)->default(0);
            $table->decimal('platform_fee', 12, 2)->default(0);
            $table->decimal('broker_commission', 12, 2)->default(0);
            $table->decimal('insurance_fee', 12, 2)->default(0);
            $table->enum('status', ['collecting', 'held', 'releasing', 'released', 'dispute_hold', 'refunded'])->default('collecting');
            $table->enum('release_trigger', ['mutual_confirmation', 'auto_24h', 'admin_override'])->nullable();
            $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('released_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escrow_accounts');
    }
};
