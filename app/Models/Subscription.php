<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'trial_ends_at'        => 'datetime',
            'current_period_start' => 'datetime',
            'current_period_end'   => 'datetime',
            'cancelled_at'         => 'datetime',
        ];
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->current_period_end
            && $this->current_period_end->isFuture();
    }

    public function onTrial(): bool
    {
        return $this->status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    public function isUsable(): bool
    {
        return $this->isActive() || $this->onTrial();
    }

    public function daysLeft(): int
    {
        $end = $this->onTrial() ? $this->trial_ends_at : $this->current_period_end;

        if (! $end || $end->isPast()) {
            return 0;
        }

        return (int) ceil(now()->diffInDays($end, false));
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'trial'     => 'تجربة مجانية',
            'active'    => 'نشط',
            'past_due'  => 'متأخر السداد',
            'cancelled' => 'ملغى',
            'expired'   => 'منتهٍ',
            default     => $this->status,
        };
    }
}
