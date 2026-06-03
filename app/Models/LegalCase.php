<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class LegalCase extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $table = 'cases';

    protected $guarded = [];

    public $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('office', function ($query) {
            if (auth()->check() && auth()->user()->office_id) {
                $query->where('cases.office_id', auth()->user()->office_id);
            }
        });
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'new'      => 'جديدة',
            'active'   => 'نشطة',
            'pending'  => 'معلقة',
            'closed'   => 'مغلقة',
            'archived' => 'مؤرشفة',
            default    => $this->status,
        };
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lawyers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'case_user', 'case_id', 'user_id')->withPivot('role')->withTimestamps();
    }

    public function hearings(): HasMany
    {
        return $this->hasMany(Hearing::class, 'case_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'case_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'case_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'case_id');
    }

    public function powersOfAttorney(): HasMany
    {
        return $this->hasMany(PowerOfAttorney::class, 'case_id');
    }

    public function aiResults()
    {
        return $this->morphMany(AIResult::class, 'model');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'case_id');
    }

    public function communications(): HasMany
    {
        return $this->hasMany(CommunicationLog::class, 'case_id');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class, 'case_id');
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(CaseDeadline::class, 'case_id');
    }
}
