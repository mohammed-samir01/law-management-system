<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Office extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $guarded = [];

    public $translatable = ['name', 'address'];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_active' => 'boolean',
        ];
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
}
