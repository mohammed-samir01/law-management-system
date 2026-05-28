<?php

namespace App\Filament\Resources\AIResultResource\Pages;

use App\Filament\Resources\AIResultResource;
use App\Jobs\AIProcessJob;
use App\Models\Document;
use App\Models\LegalCase;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAIResult extends CreateRecord
{
    protected static string $resource = AIResultResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $action   = $data['action'];
        $language = $data['language'] ?? 'ar';

        $morphable = match(true) {
            in_array($action, ['summarize_document', 'analyze_contract']) && !empty($data['document_id'])
                => Document::findOrFail($data['document_id']),
            in_array($action, ['summarize_case', 'suggest_strategy']) && !empty($data['case_id'])
                => LegalCase::findOrFail($data['case_id']),
            default => throw new \InvalidArgumentException(__('ai.no_subject_selected')),
        };

        AIProcessJob::dispatch($morphable, $action, $language, auth()->id());

        Notification::make()
            ->title(__('ai.request_queued'))
            ->body(__('ai.request_queued_body'))
            ->success()
            ->send();

        // Return a placeholder so Filament doesn't crash — actual result is created by the job
        return new \App\Models\AIResult([
            'office_id'   => auth()->user()->office_id,
            'result_type' => $action,
            'content'     => __('ai.processing'),
            'model_used'  => 'gpt-4o',
            'tokens_used' => 0,
            'created_by'  => auth()->id(),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
