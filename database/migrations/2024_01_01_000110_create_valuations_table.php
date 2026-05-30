<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('valuer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->enum('purpose', ['sale','mortgage','insurance','other']);
            $table->decimal('market_value', 15, 2)->nullable();
            $table->decimal('forced_sale_value', 15, 2)->nullable();
            $table->enum('status', ['pending','in_progress','completed','delivered'])->default('pending');
            $table->string('report_url')->nullable();
            $table->decimal('fee', 10, 2);
            $table->date('scheduled_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valuations');
    }
};
