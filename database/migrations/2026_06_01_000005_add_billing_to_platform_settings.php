<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_settings', function (Blueprint $table) {
            $table->string('billing_gateway')->nullable()->after('data');
            $table->text('billing_config')->nullable()->after('billing_gateway');
            $table->boolean('billing_test_mode')->default(true)->after('billing_config');
        });
    }

    public function down(): void
    {
        Schema::table('platform_settings', function (Blueprint $table) {
            $table->dropColumn(['billing_gateway', 'billing_config', 'billing_test_mode']);
        });
    }
};
