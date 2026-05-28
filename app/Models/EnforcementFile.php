<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class EnforcementFile extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $guarded = [];

    public $translatable = ['title', 'debtor_name', 'creditor_name'];

    protected function casts(): array
    {
        return [
            'debt_amount' => 'decimal:2',
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
            'active'    => 'نشط',
            'completed' => 'منتهي',
            'withdrawn' => 'مسحوب',
            default     => $this->status,
        };
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(EnforcementStage::class);
    }

    public function powersOfAttorney(): HasMany
    {
        return $this->hasMany(PowerOfAttorney::class);
    }
}
