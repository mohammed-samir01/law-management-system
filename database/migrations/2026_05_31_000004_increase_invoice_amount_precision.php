<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('amount',       15, 2)->change();
            $table->decimal('tax_amount',   15, 2)->change();
            $table->decimal('total_amount', 15, 2)->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('amount',       12, 2)->change();
            $table->decimal('tax_amount',   12, 2)->change();
            $table->decimal('total_amount', 12, 2)->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->change();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->change();
        });
    }
};
