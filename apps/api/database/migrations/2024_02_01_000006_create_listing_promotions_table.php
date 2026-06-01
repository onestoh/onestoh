<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listing_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['featured_listing', 'category_placement', 'homepage_banner']);
            $table->decimal('amount_paid', 10, 2);
            $table->date('starts_at');
            $table->date('ends_at');
            $table->enum('status', ['pending_payment', 'active', 'expired', 'cancelled']);
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('bookings_from_promo')->default(0);
            $table->string('payment_reference')->nullable();
            $table->timestamps();
            $table->index(['status', 'ends_at']);
            $table->index(['asset_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_promotions');
    }
};
