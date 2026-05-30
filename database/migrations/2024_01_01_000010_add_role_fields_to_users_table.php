<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'admin','landlord','broker_licensed','broker_unlicensed','tenant',
                'developer','valuer','surveyor','auctioneer','investor',
                'corporate','property_manager','finance'
            ])->default('tenant')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('avatar')->nullable()->after('phone');
            $table->boolean('is_verified')->default(false)->after('avatar');
            $table->enum('verification_tier', ['none','basic','professional','elite'])->default('none')->after('is_verified');
            $table->string('referral_code')->unique()->nullable()->after('verification_tier');
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete()->after('referral_code');
            $table->text('bio')->nullable()->after('referred_by');
            $table->boolean('is_active')->default(true)->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn([
                'role','phone','avatar','is_verified','verification_tier',
                'referral_code','referred_by','bio','is_active'
            ]);
        });
    }
};
