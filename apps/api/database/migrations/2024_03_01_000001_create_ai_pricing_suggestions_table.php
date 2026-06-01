<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_pricing_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->enum('duration_type', ['hourly', 'daily', 'weekly', 'monthly']);
            $table->decimal('current_rate', 10, 2)->nullable();
            $table->decimal('suggested_rate', 10, 2);
            $table->decimal('market_avg', 10, 2);
            $table->decimal('market_min', 10, 2);
            $table->decimal('market_max', 10, 2);
            $table->unsignedSmallInteger('comparable_listings_count');
            $table->enum('recommendation', ['increase', 'decrease', 'maintain']);
            $table->decimal('confidence_score', 5, 2); // 0-100
            $table->json('reasoning')->nullable(); // key factors
            $table->enum('owner_action', ['accepted', 'dismissed', 'customised'])->nullable();
            $table->decimal('owner_applied_rate', 10, 2)->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index(['asset_id', 'duration_type', 'expires_at']);
        });

        Schema::create('demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('category', 60)->nullable();
            $table->string('county', 60)->nullable();
            $table->date('forecast_date');
            $table->enum('period_type', ['day', 'week', 'month']);
            $table->decimal('predicted_demand_index', 8, 4); // relative demand score
            $table->decimal('predicted_booking_count', 8, 2);
            $table->decimal('confidence_score', 5, 2);
            $table->json('demand_drivers')->nullable(); // holidays, events
            $table->timestamps();
            $table->unique(['category', 'county', 'forecast_date', 'period_type']);
            $table->index(['forecast_date', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demand_forecasts');
        Schema::dropIfExists('ai_pricing_suggestions');
    }
};
