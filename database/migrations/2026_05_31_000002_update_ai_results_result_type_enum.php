<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE ai_results MODIFY COLUMN result_type ENUM(
            'summary',
            'analysis',
            'draft',
            'document_summary',
            'contract_analysis',
            'case_summary',
            'strategy_suggestion'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ai_results MODIFY COLUMN result_type ENUM(
            'summary',
            'analysis',
            'draft'
        ) NOT NULL");
    }
};
