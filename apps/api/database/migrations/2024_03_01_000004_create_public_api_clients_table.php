<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('client_id', 40)->unique();
            $table->string('client_secret_hash'); // bcrypt hashed
            $table->json('scopes')->nullable(); // ['listings:read', 'bookings:write']
            $table->json('allowed_ips')->nullable();
            $table->string('webhook_url')->nullable();
            $table->string('webhook_secret', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('rate_limit_per_minute')->default(60);
            $table->unsignedBigInteger('total_requests')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('api_webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_client_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 60);
            $table->json('payload');
            $table->string('signature', 128);
            $table->unsignedTinyInteger('attempt_count')->default(0);
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->enum('status', ['pending', 'delivered', 'failed', 'abandoned'])->default('pending');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();
            $table->index(['api_client_id', 'status', 'next_retry_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_webhook_deliveries');
        Schema::dropIfExists('api_clients');
    }
};
