<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstallmentPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'count'        => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('office', function ($query) {
            if (auth()->check() && auth()->user()->office_id) {
                $query->where(static::getModel()->getTable() . '.office_id', auth()->user()->office_id);
            }
        });

        static::creating(function (InstallmentPlan $plan) {
            if (auth()->check()) {
                $plan->office_id ??= auth()->user()->office_id;
                $plan->created_by ??= auth()->id();
            }
        });
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class)->orderBy('sequence');
    }

    /** Mark the plan completed when every installment is paid. */
    public function refreshStatus(): void
    {
        if ($this->installments()->where('status', '!=', 'paid')->doesntExist()) {
            $this->update(['status' => 'completed']);
            $this->invoice?->update(['status' => 'paid']);
        }
    }
}
