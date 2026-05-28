<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // payments.status — missing index
        Schema::table('payments', function (Blueprint $table) {
            $table->index('status');
        });

        // support_tickets — status + composite missing
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->index('status');
            $table->index(['office_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['office_id', 'status']);
        });
    }
};
