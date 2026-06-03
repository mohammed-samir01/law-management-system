<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->foreignId('case_id')->constrained('cases')->cascadeOnDelete();
            $table->enum('type', ['appeal', 'cassation', 'objection', 'grievance', 'reconsideration', 'custom'])->default('appeal');
            $table->enum('jurisdiction', ['eg', 'sa'])->nullable();
            $table->date('basis_date');   // verdict / notification date
            $table->date('due_date');     // computed deadline
            $table->unsignedSmallInteger('duration_days');
            $table->json('title')->nullable();   // translatable
            $table->json('notes')->nullable();   // translatable
            $table->enum('status', ['open', 'met', 'lapsed', 'cancelled'])->default('open');
            $table->json('alert_offsets')->nullable();
            $table->timestamp('met_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['office_id', 'status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_deadlines');
    }
};
