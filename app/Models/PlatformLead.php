<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformLead extends Model
{
    protected $guarded = [];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new'     => 'جديدة',
            'read'    => 'مقروءة',
            'replied' => 'تم الرد',
            'closed'  => 'مغلقة',
            default   => $this->status,
        };
    }
}
