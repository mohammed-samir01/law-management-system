<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'          => $this->id,
            'name'        => $this->getTranslation('name', $locale, false) ?: $this->getTranslation('name', 'ar', false),
            'email'       => $this->email,
            'phone'       => $this->phone,
            'client_type' => $this->client_type,
            'national_id' => $this->national_id,
            'address'     => $this->getTranslation('address', $locale, false) ?: $this->getTranslation('address', 'ar', false),
            'notes'       => $this->getTranslation('notes', $locale, false) ?: $this->getTranslation('notes', 'ar', false),
            'is_active'   => $this->is_active,
            'cases'       => $this->whenLoaded('cases', fn() =>
                LegalCaseResource::collection($this->cases)
            ),
            'created_at'  => $this->created_at,
        ];
    }
}
