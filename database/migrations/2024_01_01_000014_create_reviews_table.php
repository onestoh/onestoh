<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('reviewee_id');
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->tinyInteger('overall_rating');
            $table->tinyInteger('condition_rating')->nullable();
            $table->tinyInteger('punctuality_rating')->nullable();
            $table->tinyInteger('value_rating')->nullable();
            $table->tinyInteger('driver_rating')->nullable();
            $table->text('comment');
            $table->enum('would_rent_again', ['yes','no','maybe'])->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('admin_flagged')->default(false);
            $table->text('owner_response')->nullable();
            $table->timestamps();
            $table->foreign('reviewer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('reviewee_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('reviews');
    }
};
