<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class PowerOfAttorney extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $table = 'powers_of_attorney';

    protected $guarded = [];

    public $translatable = ['representative_name', 'authorities'];

    protected function casts(): array
    {
        return [
            'valid_from'  => 'date',
            'valid_until' => 'date',
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
            'active'  => 'نشطة',
            'expired' => 'منتهية',
            'revoked' => 'ملغاة',
            default   => $this->status,
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

    public function enforcementFile(): BelongsTo
    {
        return $this->belongsTo(EnforcementFile::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
