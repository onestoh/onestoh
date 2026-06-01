<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_analytics_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('revenue_this_month', 12, 2)->default(0);
            $table->decimal('revenue_last_month', 12, 2)->default(0);
            $table->decimal('revenue_this_year', 12, 2)->default(0);
            $table->decimal('utilisation_rate_30d', 5, 2)->default(0);
            $table->decimal('utilisation_rate_90d', 5, 2)->default(0);
            $table->unsignedSmallInteger('bookings_this_month')->default(0);
            $table->unsignedSmallInteger('bookings_last_month')->default(0);
            $table->decimal('avg_booking_duration_days', 5, 2)->default(0);
            $table->unsignedSmallInteger('idle_days_last_30')->default(0);
            $table->decimal('maintenance_cost_ytd', 12, 2)->default(0);
            $table->decimal('net_margin_this_month', 12, 2)->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_analytics_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');
            $table->string('metric');
            $table->string('dimension')->nullable();
            $table->decimal('value', 15, 4);
            $table->timestamps();
            $table->unique(['snapshot_date', 'metric', 'dimension']);
            $table->index(['metric', 'snapshot_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_analytics_snapshots');
        Schema::dropIfExists('asset_analytics_cache');
    }
};
