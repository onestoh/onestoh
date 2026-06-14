<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('raised_by');
            $table->unsignedBigInteger('against_id');
            $table->string('type', 100);
            $table->text('description');
            $table->enum('status', ['open','under_review','resolved','appealed'])->default('open');
            $table->json('evidence')->nullable();
            $table->text('admin_ruling')->nullable();
            $table->unsignedBigInteger('ruling_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->decimal('refund_to_client', 12, 2)->default(0);
            $table->decimal('retain_by_owner', 12, 2)->default(0);
            $table->timestamps();
            $table->foreign('raised_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('against_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ruling_by')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('disputes');
    }
};
