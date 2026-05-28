<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalCaseDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'          => $this->id,
            'case_number' => $this->case_number,
            'title'       => $this->getTranslation('title', $locale, false) ?: $this->getTranslation('title', 'ar', false),
            'description' => $this->getTranslation('description', $locale, false) ?: $this->getTranslation('description', 'ar', false),
            'case_type'   => $this->case_type,
            'status'      => $this->status,
            'status_label'=> $this->status_label,
            'court'       => $this->court,
            'judge'       => $this->judge,
            'opponent'    => $this->opponent,
            'filed_at'    => $this->filed_at,
            'closed_at'   => $this->closed_at,
            'client'      => $this->whenLoaded('client', fn() => [
                'id'    => $this->client->id,
                'name'  => $this->client->getTranslation('name', $locale, false) ?: $this->client->getTranslation('name', 'ar', false),
                'email' => $this->client->email,
                'phone' => $this->client->phone,
            ]),
            'lawyers'     => $this->whenLoaded('lawyers', fn() =>
                $this->lawyers->map(fn($l) => [
                    'id'   => $l->id,
                    'name' => $l->name,
                    'role' => $l->pivot->role,
                ])
            ),
            'hearings'    => $this->whenLoaded('hearings', fn() =>
                HearingResource::collection($this->hearings)
            ),
            'documents'   => $this->whenLoaded('documents', fn() =>
                DocumentResource::collection($this->documents)
            ),
            'created_at'  => $this->created_at,
        ];
    }
}
