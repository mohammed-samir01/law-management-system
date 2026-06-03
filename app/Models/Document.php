<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Document extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia, SoftDeletes;

    protected $guarded = [];

    public $translatable = ['title', 'content'];

    protected function casts(): array
    {
        return [
            'version'            => 'integer',
            'signing_expires_at' => 'datetime',
            'signed_at'          => 'datetime',
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

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')->useDisk('public');
    }

    public function documentable()
    {
        return $this->morphTo();
    }

    public function signingClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'signing_client_id');
    }

    /**
     * Best-effort resolution of the client this document belongs to,
     * via its morph parent (LegalCase has a client).
     */
    public function resolveClient(): ?Client
    {
        $parent = $this->documentable;

        if ($parent instanceof LegalCase) {
            return $parent->client;
        }

        if ($parent instanceof Hearing) {
            return $parent->legalCase?->client;
        }

        return null;
    }
}
