<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'date',
            'billed'      => 'boolean',
            'minutes'     => 'integer',
            'rate'        => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('office', function ($query) {
            if (auth()->check() && auth()->user()->office_id) {
                $query->where(static::getModel()->getTable() . '.office_id', auth()->user()->office_id);
            }
        });

        static::creating(function (TimeEntry $entry) {
            if (auth()->check()) {
                $entry->office_id ??= auth()->user()->office_id;
                $entry->created_by ??= auth()->id();
                $entry->user_id ??= auth()->id();
            }
            $entry->occurred_at ??= now();
        });
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** Billable amount = (minutes / 60) * hourly rate. */
    public function getAmountAttribute(): float
    {
        return round(($this->minutes / 60) * (float) ($this->rate ?? 0), 2);
    }
}
