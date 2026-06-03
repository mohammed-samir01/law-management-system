<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeAddon extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'expires_at'   => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(Addon::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(AddonPayment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function daysLeft(): int
    {
        if (! $this->expires_at || $this->expires_at->isPast()) {
            return 0;
        }

        return (int) ceil(now()->diffInDays($this->expires_at, false));
    }
}
