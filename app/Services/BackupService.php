<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class BackupService
{
    /**
     * Tables exported in an office backup. Every table here MUST have an
     * office_id column so the export stays strictly tenant-scoped.
     */
    private array $tables = [
        'clients', 'cases', 'hearings', 'documents', 'expenses', 'payments',
        'invoices', 'tasks', 'communication_logs', 'time_entries',
        'case_deadlines', 'installment_plans', 'installments',
        'enforcement_files', 'powers_of_attorney',
    ];

    /**
     * Build a tenant-scoped backup array for a single office. Read-only.
     */
    public function exportOffice(int $officeId): array
    {
        $data = [
            'meta' => [
                'office_id'    => $officeId,
                'generated_at' => now()->toIso8601String(),
                'app'          => config('app.name'),
            ],
            'tables' => [],
        ];

        foreach ($this->tables as $table) {
            if (! \Illuminate\Support\Facades\Schema::hasTable($table)) {
                continue;
            }

            // Explicit office_id filter — never rely on a global scope here.
            $data['tables'][$table] = DB::table($table)
                ->where('office_id', $officeId)
                ->get()
                ->map(fn ($row) => (array) $row)
                ->all();
        }

        return $data;
    }
}
