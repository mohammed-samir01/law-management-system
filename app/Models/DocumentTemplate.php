<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class DocumentTemplate extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = [];

    public $translatable = ['name'];

    protected function casts(): array
    {
        return [
            'placeholders' => 'array',
            'is_active' => 'boolean',
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
}
