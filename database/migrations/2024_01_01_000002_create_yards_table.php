<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('yards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('county')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('business_reg_number')->nullable();
            $table->string('kra_pin')->nullable();
            $table->string('ntsa_cert')->nullable();
            $table->enum('status', ['pending','active','suspended'])->default('pending');
            $table->enum('verification_tier', ['basic','business','premium'])->default('basic');
            $table->integer('total_ratings')->default(0);
            $table->integer('rating_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('yards');
    }
};
