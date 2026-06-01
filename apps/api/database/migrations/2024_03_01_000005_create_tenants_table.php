<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // White-label multi-tenant support (YardOS)
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 60)->unique();
            $table->string('domain')->unique()->nullable();
            $table->string('logo_url')->nullable();
            $table->string('primary_color', 7)->default('#E8922A');
            $table->string('country_code', 2)->default('KE');
            $table->string('default_currency', 3)->default('KES');
            $table->string('timezone', 40)->default('Africa/Nairobi');
            $table->json('active_payment_gateways')->nullable();
            $table->decimal('platform_fee_pct', 5, 2)->default(10.00);
            $table->decimal('referral_commission_pct', 5, 2)->default(3.00);
            $table->boolean('is_active')->default(true);
            $table->enum('plan', ['starter', 'growth', 'enterprise'])->default('starter');
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->decimal('per_transaction_fee_pct', 5, 2)->default(0.5);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tenant_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['super_admin', 'admin', 'support'])->default('admin');
            $table->timestamps();
            $table->unique(['tenant_id', 'user_id']);
        });

        // Add tenant_id to core tables for multi-tenancy
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_admins');
        Schema::dropIfExists('tenants');
    }
};
