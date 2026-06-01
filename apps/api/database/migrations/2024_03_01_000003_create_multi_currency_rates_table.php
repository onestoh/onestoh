<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 3)->default('KES');
            $table->string('target_currency', 3);
            $table->decimal('rate', 16, 8);
            $table->string('source', 30)->default('open_exchange_rates');
            $table->timestamp('fetched_at');
            $table->timestamps();
            $table->unique(['base_currency', 'target_currency', 'fetched_at']);
            $table->index(['base_currency', 'target_currency']);
        });

        // Add currency fields to users and assets
        Schema::table('users', function (Blueprint $table) {
            $table->string('preferred_currency', 3)->default('KES')->after('county');
            $table->string('country_code', 2)->default('KE')->after('preferred_currency');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->string('pricing_currency', 3)->default('KES')->after('monthly_rate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
