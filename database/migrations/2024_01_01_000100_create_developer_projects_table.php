<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developer_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->integer('total_units');
            $table->integer('sold_units')->default(0);
            $table->integer('reserved_units')->default(0);
            $table->decimal('price_from', 15, 2);
            $table->decimal('price_to', 15, 2)->nullable();
            $table->enum('status', ['planning','construction','completed','selling'])->default('planning');
            $table->date('completion_date')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_projects');
    }
};
