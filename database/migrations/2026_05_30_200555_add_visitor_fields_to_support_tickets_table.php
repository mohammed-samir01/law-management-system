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
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->string('visitor_name')->nullable()->after('description');
            $table->string('visitor_email')->nullable()->after('visitor_name');
            $table->string('visitor_phone')->nullable()->after('visitor_email');
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['description', 'visitor_name', 'visitor_email', 'visitor_phone']);
        });
    }
};
