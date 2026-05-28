<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HearingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'           => $this->id,
            'case_id'      => $this->case_id,
            'hearing_type' => $this->hearing_type,
            'status'       => $this->status,
            'status_label' => $this->status_label,
            'scheduled_at' => $this->scheduled_at,
            'court'        => $this->court,
            'judge'        => $this->judge,
            'room'         => $this->room,
            'notes'        => $this->getTranslation('notes', $locale, false) ?: $this->getTranslation('notes', 'ar', false),
            'outcome'      => $this->getTranslation('outcome', $locale, false) ?: $this->getTranslation('outcome', 'ar', false),
            'legal_case'   => $this->whenLoaded('legalCase', fn() => [
                'id'          => $this->legalCase->id,
                'case_number' => $this->legalCase->case_number,
                'title'       => $this->legalCase->getTranslation('title', $locale, false) ?: $this->legalCase->getTranslation('title', 'ar', false),
            ]),
            'created_at'   => $this->created_at,
        ];
    }
}
