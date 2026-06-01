<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('corporate_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('kra_pin', 20)->nullable();
            $table->string('registration_number', 50)->nullable();
            $table->string('billing_email');
            $table->string('billing_address')->nullable();
            $table->decimal('monthly_spend_limit', 12, 2)->nullable();
            $table->decimal('per_booking_limit', 12, 2)->nullable();
            $table->boolean('requires_approval')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('po_reference_format')->nullable();
            $table->timestamps();
        });

        Schema::create('corporate_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['owner', 'admin', 'member']);
            $table->decimal('individual_spend_limit', 12, 2)->nullable();
            $table->boolean('can_approve_bookings')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['corporate_account_id', 'user_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('corporate_account_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            $table->string('po_reference', 60)->nullable()->after('corporate_account_id');
            $table->boolean('requires_corporate_approval')->default(false)->after('po_reference');
            $table->timestamp('corporate_approved_at')->nullable()->after('requires_corporate_approval');
            $table->foreignId('corporate_approved_by')->nullable()->after('corporate_approved_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_members');
        Schema::dropIfExists('corporate_accounts');
    }
};
