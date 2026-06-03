<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Addon extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $guarded = [];

    public $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'price_yearly'  => 'decimal:2',
            'is_active'     => 'boolean',
        ];
    }

    public function officeAddons(): HasMany
    {
        return $this->hasMany(OfficeAddon::class);
    }

    public function offices(): BelongsToMany
    {
        return $this->belongsToMany(Office::class, 'office_addons')
            ->withPivot(['status', 'billing_cycle', 'activated_at', 'expires_at', 'cancelled_at'])
            ->withTimestamps();
    }

    public function priceFor(string $cycle): float
    {
        return (float) ($cycle === 'yearly' ? $this->price_yearly : $this->price_monthly);
    }

    public function isFree(): bool
    {
        return $this->price_monthly == 0;
    }
}
