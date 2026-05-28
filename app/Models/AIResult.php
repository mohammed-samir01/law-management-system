<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIResult extends Model
{
    use HasFactory;

    protected $table = 'ai_results';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tokens_used' => 'integer',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function model()
    {
        return $this->morphTo();
    }
}
