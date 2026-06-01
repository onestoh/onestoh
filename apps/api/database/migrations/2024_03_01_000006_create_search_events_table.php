<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('search_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('query')->nullable();
            $table->string('category', 60)->nullable();
            $table->string('county', 60)->nullable();
            $table->string('duration_type', 20)->nullable();
            $table->decimal('price_min', 10, 2)->nullable();
            $table->decimal('price_max', 10, 2)->nullable();
            $table->unsignedSmallInteger('results_count')->default(0);
            $table->boolean('resulted_in_booking')->default(false);
            $table->string('session_id', 64)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index(['category', 'county', 'created_at']);
        });

        Schema::create('ai_chat_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_token', 64)->unique();
            $table->json('messages'); // [{role, content, created_at}]
            $table->unsignedSmallInteger('message_count')->default(0);
            $table->decimal('tokens_used', 10, 2)->default(0);
            $table->boolean('escalated_to_human')->default(false);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_sessions');
        Schema::dropIfExists('search_events');
    }
};
