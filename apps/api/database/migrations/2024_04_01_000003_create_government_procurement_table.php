<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('government_entities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_number', 50)->unique();
            $table->string('county', 60)->nullable();
            $table->string('ministry_department')->nullable();
            $table->enum('tier', ['national', 'county', 'parastatal', 'ngo']);
            $table->string('procurement_email');
            $table->string('po_box')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('procurement_tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('government_entity_id')->constrained()->cascadeOnDelete();
            $table->string('tender_number', 60)->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('asset_category', ['passenger_car', 'suv_4x4', 'van_minibus', 'pickup_truck', 'heavy_truck', 'excavator', 'tractor_farm', 'crane_lift', 'generator', 'special_equipment']);
            $table->unsignedSmallInteger('quantity_required');
            $table->enum('duration_type', ['daily', 'weekly', 'monthly']);
            $table->unsignedSmallInteger('duration_value');
            $table->decimal('budget_per_unit', 12, 2);
            $table->decimal('total_budget', 12, 2);
            $table->date('submission_deadline');
            $table->date('service_start_date');
            $table->date('service_end_date');
            $table->enum('status', ['open', 'closed', 'awarded', 'cancelled'])->default('open');
            $table->json('requirements')->nullable(); // insurance, certification reqs
            $table->timestamps();
            $table->index(['status', 'submission_deadline']);
            $table->index(['asset_category', 'status']);
        });

        Schema::create('tender_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('procurement_tenders')->cascadeOnDelete();
            $table->foreignId('yard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->json('offered_assets'); // [{asset_id, rate, availability_confirmed}]
            $table->decimal('total_bid_value', 12, 2);
            $table->decimal('rate_per_unit', 10, 2);
            $table->text('proposal_notes')->nullable();
            $table->json('compliance_documents')->nullable();
            $table->enum('status', ['submitted', 'shortlisted', 'awarded', 'rejected'])->default('submitted');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('awarded_at')->nullable();
            $table->timestamps();
            $table->unique(['tender_id', 'yard_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_bids');
        Schema::dropIfExists('procurement_tenders');
        Schema::dropIfExists('government_entities');
    }
};
