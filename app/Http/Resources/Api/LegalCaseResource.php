<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalCaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'case_number'  => $this->case_number,
            'title'        => $this->getTranslation('title', app()->getLocale(), false) ?: $this->getTranslation('title', 'ar', false),
            'case_type'    => $this->case_type,
            'status'       => $this->status,
            'status_label' => $this->status_label,
            'court'        => $this->court,
            'judge'        => $this->judge,
            'filed_at'     => $this->filed_at,
            'closed_at'    => $this->closed_at,
            'client'       => $this->whenLoaded('client', fn() => [
                'id'   => $this->client->id,
                'name' => $this->client->getTranslation('name', app()->getLocale(), false) ?: $this->client->getTranslation('name', 'ar', false),
            ]),
            'created_at'   => $this->created_at,
        ];
    }
}
