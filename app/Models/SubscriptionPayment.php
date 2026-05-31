<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount'  => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'معلق',
            'completed' => 'مكتمل',
            'failed'    => 'فاشل',
            'refunded'  => 'مُسترد',
            default     => $this->status,
        };
    }
}
