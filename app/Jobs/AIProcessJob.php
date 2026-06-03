<?php

namespace App\Jobs;

use App\Services\AIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AIProcessJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(
        private readonly Model  $morphable,
        private readonly string $action,
        private readonly string $language = 'ar',
        private readonly int    $userId = 0,
        private readonly int    $secondModelId = 0,
    ) {}

    public function handle(AIService $service): void
    {
        auth()->onceUsingId($this->userId);

        match ($this->action) {
            'summarize_document' => $service->summarizeDocument($this->morphable, $this->language),
            'analyze_contract'   => $service->analyzeContract($this->morphable, $this->language),
            'summarize_case'     => $service->summarizeCase($this->morphable, $this->language),
            'suggest_strategy'   => $service->suggestStrategy($this->morphable, $this->language),
            'draft_memo'         => $service->draftLegalMemo($this->morphable, $this->language),
            'predict_outcome'    => $service->predictOutcome($this->morphable, $this->language),
            'compare_contracts'  => $this->handleCompare($service),
            default              => Log::warning('Unknown AI action: ' . $this->action),
        };
    }

    private function handleCompare(AIService $service): void
    {
        $second = \App\Models\Document::withoutGlobalScopes()->find($this->secondModelId);

        if (! $second) {
            Log::warning('AIProcessJob compare: second document not found', ['id' => $this->secondModelId]);
            return;
        }

        $service->compareContracts($this->morphable, $second, $this->language);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('AIProcessJob failed', [
            'action' => $this->action,
            'model'  => get_class($this->morphable),
            'id'     => $this->morphable->getKey(),
            'error'  => $e->getMessage(),
        ]);

        if ($this->userId) {
            $user = \App\Models\User::find($this->userId);
            if ($user) {
                Notification::make()
                    ->title('فشل طلب الذكاء الاصطناعي')
                    ->body($e->getMessage())
                    ->danger()
                    ->sendToDatabase($user);
            }
        }
    }
}
