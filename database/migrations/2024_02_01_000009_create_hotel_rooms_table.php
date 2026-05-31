<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('room_number');
            $table->enum('room_type', ['standard', 'deluxe', 'suite', 'penthouse', 'family', 'single', 'double', 'twin']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->tinyInteger('floor')->nullable();
            $table->tinyInteger('capacity')->default(2);
            $table->decimal('price_per_night', 10, 2);
            $table->json('amenities')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
