<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legislation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->string('law_number');
            $table->json('name');
            $table->string('type')->nullable();
            $table->date('enactment_date')->nullable();
            $table->enum('status', ['active', 'repealed'])->default('active');
            $table->json('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legislation');
    }
};
