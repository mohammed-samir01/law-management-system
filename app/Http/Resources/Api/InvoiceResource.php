<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'             => $this->id,
            'invoice_number' => $this->invoice_number,
            'status'         => $this->status,
            'status_label'   => $this->status_label,
            'amount'         => $this->amount,
            'tax_amount'     => $this->tax_amount,
            'total_amount'   => $this->total_amount,
            'due_date'       => $this->due_date,
            'notes'          => $this->getTranslation('notes', $locale, false) ?: $this->getTranslation('notes', 'ar', false),
            'client'         => $this->whenLoaded('client', fn() => [
                'id'   => $this->client->id,
                'name' => $this->client->getTranslation('name', $locale, false) ?: $this->client->getTranslation('name', 'ar', false),
            ]),
            'legal_case'     => $this->whenLoaded('legalCase', fn() => [
                'id'          => $this->legalCase->id,
                'case_number' => $this->legalCase->case_number,
                'title'       => $this->legalCase->getTranslation('title', $locale, false) ?: $this->legalCase->getTranslation('title', 'ar', false),
            ]),
            'created_at'     => $this->created_at,
        ];
    }
}
