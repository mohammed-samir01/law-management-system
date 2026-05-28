<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enforcement_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->string('file_number')->unique();
            $table->json('title');
            $table->json('debtor_name');
            $table->json('creditor_name');
            $table->decimal('debt_amount', 12, 2);
            $table->enum('currency', ['SAR', 'EGP', 'USD'])->default('SAR');
            $table->enum('status', ['active', 'completed', 'withdrawn'])->default('active');
            $table->string('enforcement_office')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enforcement_files');
    }
};
