<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Cross-platform SSO tokens for YardGroup super-app
        Schema::create('yardgroup_sso_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['theonlineyard', 'estateyard', 'motoryard']);
            $table->string('platform_user_id', 60)->nullable(); // user ID on the other platform
            $table->string('sso_token', 128)->unique();
            $table->json('shared_profile')->nullable(); // shared KYC data
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index(['user_id', 'platform']);
        });

        Schema::create('yardgroup_cross_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_id')->constrained('users')->cascadeOnDelete();
            $table->string('referral_code', 12);
            $table->enum('source_platform', ['theonlineyard', 'estateyard', 'motoryard']);
            $table->enum('target_platform', ['theonlineyard', 'estateyard', 'motoryard']);
            $table->string('referred_user_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->decimal('commission_earned', 10, 2)->default(0);
            $table->enum('status', ['clicked', 'registered', 'transacted', 'commission_paid'])->default('clicked');
            $table->timestamps();
        });

        // Data marketplace for anonymised insights
        Schema::create('data_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 60)->unique();
            $table->text('description');
            $table->enum('type', ['market_report', 'pricing_index', 'demand_forecast', 'fleet_valuation_api', 'custom']);
            $table->enum('audience', ['insurance', 'financing', 'government', 'research', 'developer']);
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_annual', 12, 2);
            $table->string('api_endpoint')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('data_product_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_product_id')->constrained()->cascadeOnDelete();
            $table->string('subscriber_name');
            $table->string('subscriber_email');
            $table->string('subscriber_type', 30); // company, individual
            $table->enum('billing_cycle', ['monthly', 'annual']);
            $table->decimal('amount_paid', 10, 2);
            $table->string('api_key', 64)->unique();
            $table->timestamp('current_period_ends_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_product_subscriptions');
        Schema::dropIfExists('data_products');
        Schema::dropIfExists('yardgroup_cross_referrals');
        Schema::dropIfExists('yardgroup_sso_tokens');
    }
};
