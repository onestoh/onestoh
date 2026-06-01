<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
            $table->enum('product_type', ['collision_damage_waiver', 'third_party_liability', 'comprehensive']);
            $table->string('insurer', 60);
            $table->string('policy_number')->nullable();
            $table->decimal('premium', 10, 2);
            $table->decimal('coverage_limit', 12, 2)->nullable();
            $table->decimal('excess_amount', 10, 2)->nullable();
            $table->date('valid_from');
            $table->date('valid_to');
            $table->enum('status', ['active', 'claimed', 'expired', 'cancelled'])->default('active');
            $table->string('certificate_path')->nullable();
            $table->timestamps();
        });

        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_policy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('filed_by')->constrained('users')->cascadeOnDelete();
            $table->text('description');
            $table->decimal('claimed_amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->enum('status', ['submitted', 'under_review', 'approved', 'rejected', 'paid']);
            $table->json('evidence')->nullable();
            $table->text('insurer_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
        Schema::dropIfExists('insurance_policies');
    }
};
