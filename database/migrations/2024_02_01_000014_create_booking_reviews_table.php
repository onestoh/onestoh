<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating');
            $table->tinyInteger('cleanliness')->nullable();
            $table->tinyInteger('communication')->nullable();
            $table->tinyInteger('location')->nullable();
            $table->tinyInteger('value')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_reviews');
    }
};
