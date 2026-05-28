<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'           => $this->id,
            'name'         => $this->getTranslation('name', $locale, false) ?: $this->getTranslation('name', 'ar', false),
            'email'        => $this->email,
            'phone'        => $this->phone,
            'client_type'  => $this->client_type,
            'national_id'  => $this->national_id,
            'is_active'    => $this->is_active,
            'cases_count'  => $this->whenCounted('cases'),
            'created_at'   => $this->created_at,
        ];
    }
}
