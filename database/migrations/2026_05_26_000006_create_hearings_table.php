<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hearings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->foreignId('case_id')->constrained('cases')->cascadeOnDelete();
            $table->timestamp('scheduled_at');
            $table->string('location')->nullable();
            $table->string('court_room')->nullable();
            $table->string('judge')->nullable();
            $table->enum('status', ['scheduled', 'held', 'completed', 'adjourned', 'postponed', 'cancelled'])->default('scheduled');
            $table->json('notes')->nullable();
            $table->json('outcome')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hearings');
    }
};
