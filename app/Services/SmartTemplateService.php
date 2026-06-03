<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\LegalCase;

class SmartTemplateService
{
    /**
     * Generate a draft Document from a template, auto-filling known placeholders
     * from the given case. Extra manual placeholder values override the auto map.
     */
    public function generate(DocumentTemplate $template, ?LegalCase $case, array $overrides = []): Document
    {
        $map = array_merge($this->autoMap($case), $overrides);

        $content = (string) ($template->content ?? '');
        foreach ($map as $key => $value) {
            $content = str_replace('{{' . $key . '}}', (string) $value, $content);
        }

        $titleAr = $template->getTranslation('name', 'ar') . ' — ' . now()->format('Y/m/d');

        return Document::create([
            'office_id'         => auth()->user()->office_id,
            'title'             => ['ar' => $titleAr, 'en' => $template->getTranslation('name', 'en')],
            'type'              => $this->categoryToType($template->category),
            'category'          => $template->category,
            'status'            => 'draft',
            'version'           => 1,
            'uploaded_by'       => auth()->id(),
            'documentable_type' => $case ? LegalCase::class : null,
            'documentable_id'   => $case?->id,
            'content'           => ['ar' => $content, 'en' => ''],
        ]);
    }

    /**
     * Build the placeholder => value map from a case (and its client).
     */
    public function autoMap(?LegalCase $case): array
    {
        $map = [
            'today'      => now()->format('Y/m/d'),
            'office'     => auth()->user()?->office?->name ?? config('app.name'),
        ];

        if ($case) {
            $client = $case->client;
            $map += array_filter([
                'case_number'  => $case->case_number,
                'case_title'   => $case->getTranslation('title', 'ar') ?: $case->getTranslation('title', 'en'),
                'case_type'    => $case->type,
                'court'        => $case->court,
                'judge'        => $case->judge,
                'client_name'  => $client?->getTranslation('name', 'ar') ?: $client?->getTranslation('name', 'en'),
                'client_phone' => $client?->phone,
                'client_email' => $client?->email,
            ], fn ($v) => $v !== null && $v !== '');
        }

        return $map;
    }

    private function categoryToType(string $category): string
    {
        return match ($category) {
            'legal'     => 'contract',
            'financial' => 'other',
            'court'     => 'pleading',
            'contract'  => 'contract',
            default     => 'other',
        };
    }
}
