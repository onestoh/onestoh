<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financing_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 30); // bank, sacco, microfinance
            $table->string('logo_url')->nullable();
            $table->string('country_code', 2)->default('KE');
            $table->decimal('min_loan_amount', 12, 2);
            $table->decimal('max_loan_amount', 12, 2);
            $table->decimal('min_interest_rate_pa', 5, 2); // per annum
            $table->decimal('max_interest_rate_pa', 5, 2);
            $table->unsignedTinyInteger('min_tenure_months');
            $table->unsignedTinyInteger('max_tenure_months');
            $table->unsignedTinyInteger('min_deposit_pct'); // % of asset value
            $table->string('api_endpoint')->nullable();
            $table->string('api_key_encrypted')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('eligible_asset_categories')->nullable();
            $table->timestamps();
        });

        Schema::create('financing_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('financing_partner_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['purchase_loan', 'lease_to_own', 'fleet_credit_line']);
            $table->decimal('asset_value', 12, 2);
            $table->decimal('requested_amount', 12, 2);
            $table->decimal('deposit_amount', 12, 2);
            $table->unsignedTinyInteger('tenure_months');
            $table->decimal('interest_rate_pa', 5, 2)->nullable();
            $table->decimal('monthly_repayment', 10, 2)->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'disbursed', 'active', 'completed', 'defaulted'])->default('draft');
            $table->string('partner_reference')->nullable();
            $table->json('submitted_documents')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decision_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::create('lease_to_own_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financing_application_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lessee_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total_lease_value', 12, 2);
            $table->decimal('monthly_payment', 10, 2);
            $table->unsignedTinyInteger('total_months');
            $table->unsignedTinyInteger('months_paid')->default(0);
            $table->decimal('balloon_payment', 10, 2)->default(0);
            $table->decimal('residual_value', 12, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('next_payment_due');
            $table->enum('status', ['active', 'completed', 'defaulted', 'terminated'])->default('active');
            $table->boolean('ownership_transferred')->default(false);
            $table->timestamp('ownership_transferred_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lease_to_own_agreements');
        Schema::dropIfExists('financing_applications');
        Schema::dropIfExists('financing_partners');
    }
};
