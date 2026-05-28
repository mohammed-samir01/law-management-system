<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Hearing extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $guarded = [];

    public $translatable = ['notes', 'outcome'];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('office', function ($query) {
            if (auth()->check() && auth()->user()->office_id) {
                $query->where(static::getModel()->getTable() . '.office_id', auth()->user()->office_id);
            }
        });
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'scheduled'  => 'مجدولة',
            'completed'  => 'منتهية',
            'postponed'  => 'مؤجلة',
            'cancelled'  => 'ملغاة',
            default      => $this->status,
        };
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
}
