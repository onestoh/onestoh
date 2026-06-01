<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['photo', 'video']);
            $table->string('file_path'); // S3 path
            $table->string('thumbnail_path')->nullable();
            $table->string('file_hash', 64)->nullable(); // for duplicate detection
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('file_size')->nullable();
            $table->timestamps();
            $table->index(['asset_id', 'type', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_media');
    }
};
