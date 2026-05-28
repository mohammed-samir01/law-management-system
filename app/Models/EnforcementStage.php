<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class EnforcementStage extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = [];

    public $translatable = ['stage_name'];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'order' => 'integer',
        ];
    }

    public function enforcementFile(): BelongsTo
    {
        return $this->belongsTo(EnforcementFile::class);
    }
}
