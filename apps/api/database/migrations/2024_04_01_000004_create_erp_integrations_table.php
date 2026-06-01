<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('erp_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['quickbooks', 'xero', 'sage', 'wave', 'zoho_books']);
            $table->string('access_token_encrypted')->nullable();
            $table->string('refresh_token_encrypted')->nullable();
            $table->string('realm_id')->nullable(); // QuickBooks company ID
            $table->string('tenant_id_erp')->nullable(); // Xero tenant
            $table->json('sync_settings')->nullable(); // {sync_invoices, sync_expenses, sync_contacts}
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->unsignedInteger('records_synced')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'platform']);
        });

        Schema::create('erp_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('erp_integration_id')->constrained()->cascadeOnDelete();
            $table->enum('direction', ['push', 'pull']);
            $table->string('entity_type', 40); // invoice, expense, contact, payment
            $table->string('local_id')->nullable();
            $table->string('remote_id')->nullable();
            $table->enum('status', ['success', 'failed', 'skipped']);
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['erp_integration_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_sync_logs');
        Schema::dropIfExists('erp_integrations');
    }
};
