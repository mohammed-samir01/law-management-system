<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // null = unlimited
            $table->unsignedInteger('max_ai_requests_monthly')->nullable()->after('ai_enabled');
            $table->unsignedBigInteger('max_ai_tokens_monthly')->nullable()->after('max_ai_requests_monthly');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['max_ai_requests_monthly', 'max_ai_tokens_monthly']);
        });
    }
};
