<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', [
                'national_id_front', 'national_id_back', 'selfie_with_id',
                'driving_licence', 'business_certificate', 'kra_pin_certificate',
                'ntsa_certificate', 'police_clearance', 'operator_certificate',
                'proof_of_address', 'broker_registration', 'company_bank_details'
            ]);
            $table->string('file_path'); // S3 path
            $table->string('file_name');
            $table->string('mime_type', 50);
            $table->unsignedInteger('file_size'); // bytes
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'document_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_documents');
    }
};
