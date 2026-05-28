<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enforcement_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enforcement_file_id')->constrained('enforcement_files')->cascadeOnDelete();
            $table->json('stage_name');
            $table->unsignedInteger('order')->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enforcement_stages');
    }
};
