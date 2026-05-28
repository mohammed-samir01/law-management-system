<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'            => $this->id,
            'title'         => $this->getTranslation('title', $locale, false) ?: $this->getTranslation('title', 'ar', false),
            'document_type' => $this->document_type,
            'file_path'     => $this->file_path,
            'file_size'     => $this->file_size,
            'mime_type'     => $this->mime_type,
            'created_at'    => $this->created_at,
        ];
    }
}
