<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->unsignedInteger('max_users')->default(5);
            $table->unsignedInteger('max_cases')->default(50);
            $table->unsignedInteger('max_storage_mb')->default(1024);
            $table->boolean('ai_enabled')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
