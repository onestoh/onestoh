<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('type', ['house','apartment','land','commercial','airbnb','hotel','villa','office']);
            $table->enum('listing_type', ['sale','rent','airbnb','hotel','auction']);
            $table->enum('status', ['active','pending','sold','rented','draft','suspended'])->default('active');
            $table->decimal('price', 15, 2);
            $table->enum('price_period', ['night','month','year'])->nullable();
            $table->tinyInteger('bedrooms')->unsigned()->nullable();
            $table->tinyInteger('bathrooms')->unsigned()->nullable();
            $table->decimal('area_sqft', 10, 2)->nullable();
            $table->tinyInteger('floors')->unsigned()->nullable();
            $table->year('year_built')->nullable();
            $table->string('county');
            $table->string('constituency')->nullable();
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('amenities')->nullable();
            $table->json('images')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('save_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
