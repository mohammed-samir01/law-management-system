<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deadline_alert_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->foreignId('case_deadline_id')->constrained('case_deadlines')->cascadeOnDelete();
            $table->unsignedSmallInteger('offset_days');
            $table->string('channel')->default('app');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // idempotency: one alert per (deadline, offset, channel)
            $table->unique(['case_deadline_id', 'offset_days', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deadline_alert_logs');
    }
};
