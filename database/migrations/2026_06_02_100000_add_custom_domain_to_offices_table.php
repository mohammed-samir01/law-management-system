<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->string('custom_domain')->nullable()->unique()->after('slug');
            $table->string('domain_verify_token')->nullable()->after('custom_domain');
            $table->timestamp('domain_verified_at')->nullable()->after('domain_verify_token');
        });
    }

    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn(['custom_domain', 'domain_verify_token', 'domain_verified_at']);
        });
    }
};
