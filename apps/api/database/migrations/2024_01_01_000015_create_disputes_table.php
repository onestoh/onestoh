<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_id')->unique();
            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
            $table->foreignId('raised_by')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['damage', 'not_as_described', 'no_show', 'mileage_overage', 'deposit_retention', 'early_termination', 'other']);
            $table->text('description');
            $table->json('evidence')->nullable(); // {photos: [...], notes: "..."}
            $table->json('respondent_evidence')->nullable();
            $table->enum('status', ['open', 'under_review', 'ruled', 'appealed', 'closed'])->default('open');
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('ruling')->nullable();
            $table->json('escrow_split')->nullable(); // {owner: 5000, client: 2000}
            $table->boolean('appeal_used')->default(false);
            $table->timestamp('appeal_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
