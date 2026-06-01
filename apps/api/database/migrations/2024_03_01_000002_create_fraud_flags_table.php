<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fraud_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->enum('flag_type', [
                'multiple_bookings_same_ip', 'rapid_kyc_resubmission',
                'unusual_payout_velocity', 'duplicate_id_document',
                'suspicious_cancellation_pattern', 'card_testing',
                'multiple_accounts_same_device', 'abnormal_booking_value'
            ]);
            $table->decimal('risk_score', 5, 2); // 0-100
            $table->text('details')->nullable();
            $table->json('evidence')->nullable();
            $table->enum('status', ['open', 'investigating', 'confirmed_fraud', 'false_positive', 'resolved'])->default('open');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['flag_type', 'status', 'created_at']);
        });

        Schema::create('fraud_risk_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('overall_risk_score', 5, 2)->default(0);
            $table->unsignedSmallInteger('flags_count')->default(0);
            $table->unsignedSmallInteger('confirmed_fraud_count')->default(0);
            $table->string('last_known_ip', 45)->nullable();
            $table->string('device_fingerprint', 64)->nullable();
            $table->json('ip_history')->nullable();
            $table->json('risk_factors')->nullable();
            $table->timestamp('last_assessed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_risk_profiles');
        Schema::dropIfExists('fraud_flags');
    }
};
