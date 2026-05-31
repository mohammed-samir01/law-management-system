<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing 'active' records to 'published'
        DB::statement("UPDATE case_laws SET status = 'published' WHERE status = 'active'");

        DB::statement("ALTER TABLE case_laws MODIFY COLUMN status ENUM('published', 'draft', 'archived') NOT NULL DEFAULT 'published'");
    }

    public function down(): void
    {
        DB::statement("UPDATE case_laws SET status = 'active' WHERE status = 'published'");
        DB::statement("UPDATE case_laws SET status = 'archived' WHERE status = 'draft'");

        DB::statement("ALTER TABLE case_laws MODIFY COLUMN status ENUM('active', 'archived') NOT NULL DEFAULT 'active'");
    }
};
