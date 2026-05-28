<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->string('case_number')->unique();
            $table->enum('type', ['civil', 'criminal', 'family', 'labor', 'commercial', 'administrative', 'real_estate']);
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('court')->nullable();
            $table->string('judge')->nullable();
            $table->enum('status', ['new', 'active', 'pending', 'adjourned', 'closed', 'archived'])->default('new');
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
