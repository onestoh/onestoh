<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();
            $table->enum('reviewer_type', ['client', 'owner']);
            $table->unsignedTinyInteger('overall_rating'); // 1-5
            $table->json('sub_ratings')->nullable(); // {condition: 4, timeliness: 5, ...}
            $table->text('comment');
            $table->json('tags')->nullable(); // positive/negative tags
            $table->json('photos')->nullable();
            $table->text('owner_response')->nullable();
            $table->timestamp('owner_responded_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('admin_flagged')->default(false);
            $table->text('flag_reason')->nullable();
            $table->timestamps();
            $table->unique(['booking_id', 'reviewer_id']);
            $table->index(['reviewee_id', 'reviewer_type', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
