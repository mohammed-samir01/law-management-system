<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM('draft', 'final', 'approved', 'archived') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("UPDATE documents SET status = 'approved' WHERE status = 'final'");
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM('draft', 'approved', 'archived') NOT NULL DEFAULT 'draft'");
    }
};
