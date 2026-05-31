<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('hotel_rooms')->nullOnDelete();
            $table->tinyInteger('day_of_week')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->decimal('price_per_night', 10, 2);
            $table->tinyInteger('minimum_nights')->default(1);
            $table->tinyInteger('priority')->default(0);
            $table->string('label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_pricing_rules');
    }
};
