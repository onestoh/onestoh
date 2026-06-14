<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mfa_code')->nullable()->after('remember_token');
            $table->timestamp('mfa_code_expires_at')->nullable()->after('mfa_code');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mfa_code','mfa_code_expires_at']);
        });
    }
};
