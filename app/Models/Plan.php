<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Plan extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = [];

    public $translatable = ['name'];

    protected function casts(): array
    {
        return [
            'price_monthly'   => 'decimal:2',
            'price_yearly'    => 'decimal:2',
            'features'        => 'array',
            'ai_enabled'      => 'boolean',
            'custom_branding' => 'boolean',
            'is_active'       => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function priceFor(string $cycle): float
    {
        return (float) ($cycle === 'yearly' ? $this->price_yearly : $this->price_monthly);
    }

    public function isFree(): bool
    {
        return (float) $this->price_monthly == 0.0;
    }
}
