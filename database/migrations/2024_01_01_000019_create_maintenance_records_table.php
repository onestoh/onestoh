<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('service_type', 100);
            $table->decimal('cost', 10, 2);
            $table->string('service_centre', 255)->nullable();
            $table->date('service_date');
            $table->date('next_due_date')->nullable();
            $table->integer('next_due_km')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('maintenance_records');
    }
};
