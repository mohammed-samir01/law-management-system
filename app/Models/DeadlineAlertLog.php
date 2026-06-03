<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeadlineAlertLog extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'sent_at'     => 'datetime',
            'offset_days' => 'integer',
        ];
    }

    public function deadline(): BelongsTo
    {
        return $this->belongsTo(CaseDeadline::class, 'case_deadline_id');
    }
}
