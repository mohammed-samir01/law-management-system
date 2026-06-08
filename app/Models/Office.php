<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Office extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $guarded = [];

    public $translatable = ['name', 'address'];

    protected static function booted(): void
    {
        static::deleting(function (Office $office) {
            $office->users()->delete();
        });

        // Reset verification and generate a new token when domain changes
        static::saving(function (Office $office) {
            if ($office->isDirty('custom_domain')) {
                $office->domain_verified_at = null;
                $office->domain_verify_token = $office->custom_domain
                    ? 'mizan-verify=' . Str::random(32)
                    : null;
            }
        });

        // Bust the domain cache whenever domain-related fields change
        static::saved(function (Office $office) {
            if ($office->wasChanged(['custom_domain', 'domain_verified_at', 'is_active'])) {
                $old = $office->getOriginal('custom_domain');
                $new = $office->custom_domain;
                if ($old) Cache::forget("custom_domain:{$old}");
                if ($new) Cache::forget("custom_domain:{$new}");
            }
        });
    }

    protected function casts(): array
    {
        return [
            'settings'           => 'array',
            'is_active'          => 'boolean',
            'domain_verified_at' => 'datetime',
        ];
    }

    // ── Domain helpers ────────────────────────────────────────────────────────

    public function isDomainVerified(): bool
    {
        return $this->domain_verified_at !== null;
    }

    /** Normalize a raw domain string: lowercase, no scheme, no trailing slash */
    public static function normalizeDomain(string $raw): string
    {
        $host = strtolower(trim($raw));
        $host = preg_replace('#^https?://#', '', $host);
        return rtrim($host, '/');
    }

    /** Scope: match a verified, active, non-deleted office by its custom domain */
    public function scopeByVerifiedDomain(Builder $query, string $host): Builder
    {
        return $query
            ->whereNotNull('domain_verified_at')
            ->whereNotNull('custom_domain')
            ->where('custom_domain', $host)
            ->where('is_active', true)
            ->whereNull('deleted_at');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(LegalCase::class);
    }

    public function hearings(): HasMany
    {
        return $this->hasMany(Hearing::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function paymentGateways(): HasMany
    {
        return $this->hasMany(PaymentGateway::class);
    }

    public function enforcementFiles(): HasMany
    {
        return $this->hasMany(EnforcementFile::class);
    }

    public function powersOfAttorney(): HasMany
    {
        return $this->hasMany(PowerOfAttorney::class);
    }

    public function legislation(): HasMany
    {
        return $this->hasMany(Legislation::class);
    }

    public function caseLaws(): HasMany
    {
        return $this->hasMany(CaseLaw::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function aiResults(): HasMany
    {
        return $this->hasMany(AIResult::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function subscriptionPayments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function hasUsableSubscription(): bool
    {
        return $this->subscription?->isUsable() ?? false;
    }

    public function activePlan(): ?Plan
    {
        $sub = $this->subscription;

        return ($sub && $sub->isUsable()) ? $sub->plan : null;
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class, 'office_addons')
            ->withPivot(['status', 'billing_cycle', 'activated_at', 'expires_at', 'cancelled_at'])
            ->withTimestamps();
    }

    public function activeAddons(): Collection
    {
        // Cache per model instance for the lifetime of the request.
        if (! isset($this->relations['_active_addons'])) {
            $this->relations['_active_addons'] = $this->addons()
                ->wherePivot('status', 'active')
                ->get();
        }

        return $this->relations['_active_addons'];
    }

    public function hasAddon(string $slug): bool
    {
        return $this->activeAddons()->contains('slug', $slug);
    }
}
