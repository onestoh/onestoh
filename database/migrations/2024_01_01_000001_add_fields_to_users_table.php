<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin','yard_owner','individual_owner','client','broker','operator'])->default('client')->after('password');
            $table->string('phone', 20)->nullable()->after('role');
            $table->string('national_id', 50)->nullable()->after('phone');
            $table->enum('status', ['pending','verified','suspended','rejected'])->default('pending')->after('national_id');
            $table->integer('trust_score')->default(100)->after('status');
            $table->enum('verification_tier', ['unverified','id_verified','business_verified','premium_verified'])->default('unverified')->after('trust_score');
            $table->string('referral_code', 20)->nullable()->unique()->after('verification_tier');
            $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code');
            $table->string('avatar', 255)->nullable()->after('referred_by');
            $table->foreign('referred_by')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['role','phone','national_id','status','trust_score','verification_tier','referral_code','referred_by','avatar']);
        });
    }
};
