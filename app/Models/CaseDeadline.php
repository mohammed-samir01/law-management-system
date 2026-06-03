<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CaseDeadline extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $guarded = [];

    public $translatable = ['title', 'notes'];

    protected function casts(): array
    {
        return [
            'basis_date'    => 'date',
            'due_date'      => 'date',
            'met_at'        => 'datetime',
            'alert_offsets' => 'array',
            'duration_days' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('office', function ($query) {
            if (auth()->check() && auth()->user()->office_id) {
                $query->where(static::getModel()->getTable() . '.office_id', auth()->user()->office_id);
            }
        });

        static::creating(function (CaseDeadline $d) {
            if (auth()->check()) {
                $d->office_id ??= auth()->user()->office_id;
                $d->created_by ??= auth()->id();
            }
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function alertLogs(): HasMany
    {
        return $this->hasMany(DeadlineAlertLog::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function daysLeft(): int
    {
        return $this->due_date ? (int) now()->startOfDay()->diffInDays($this->due_date, false) : 0;
    }
}
