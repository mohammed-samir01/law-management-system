<?php

namespace App\Http\Controllers\Mobile\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Hearing;
use App\Models\LegalCase;
use App\Notifications\AiHearingReminderNotification;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    public function caseSummary(int $id): JsonResponse
    {
        $case = LegalCase::with(['hearings', 'documents', 'client'])->findOrFail($id);

        try {
            $result = app(AIService::class)->summarizeCase($case, 'ar');
            return response()->json(['summary' => $result->content]);
        } catch (\Throwable $e) {
            Log::error('AI case summary failed', ['case_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'فشل توليد الملخص — تحقق من إعدادات OpenAI'], 500);
        }
    }

    public function analyzeDocument(int $caseId, int $docId): JsonResponse
    {
        LegalCase::findOrFail($caseId);

        $document = Document::where('legal_case_id', $caseId)->findOrFail($docId);

        try {
            $result = app(AIService::class)->summarizeDocument($document, 'ar');
            return response()->json(['analysis' => $result->content]);
        } catch (\Throwable $e) {
            Log::error('AI document analysis failed', ['doc_id' => $docId, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'فشل تحليل المستند — تحقق من إعدادات OpenAI'], 500);
        }
    }

    public function generateReminder(int $hearingId): JsonResponse
    {
        $hearing = Hearing::with(['legalCase.client'])->findOrFail($hearingId);

        $clientName = $hearing->legalCase?->client?->getTranslation('name', 'ar', false) ?? 'الموكل الكريم';
        $caseTitle  = $hearing->legalCase?->getTranslation('title', 'ar', false) ?? 'القضية';
        $date       = $hearing->scheduled_at->translatedFormat('l d/m/Y');
        $time       = $hearing->scheduled_at->format('H:i');
        $court      = $hearing->court ?? 'المحكمة';

        $prompt = <<<PROMPT
اكتب رسالة تذكير رسمية ومهنية بالعربية لموكل اسمه "{$clientName}" بخصوص جلسة قضية "{$caseTitle}" المقررة يوم {$date} الساعة {$time} في {$court}.
الرسالة يجب أن تكون:
- مهذبة ورسمية
- مختصرة (3-4 أسطر)
- تُذكّر الموكل بضرورة الحضور
- لا تتضمن توقيع أو تحية ختامية
PROMPT;

        try {
            $response = \OpenAI::client(config('services.openai.api_key'))
                ->chat()->create([
                    'model'       => 'gpt-4o',
                    'messages'    => [
                        ['role' => 'system', 'content' => 'أنت مساعد قانوني متخصص في المكاتب القانونية المصرية والسعودية.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens'  => 300,
                    'temperature' => 0.4,
                ]);

            $message = $response->choices[0]->message->content;
            return response()->json(['message' => $message, 'hearing_id' => $hearingId]);
        } catch (\Throwable $e) {
            Log::error('AI reminder generation failed', ['hearing_id' => $hearingId, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'فشل توليد التذكير — تحقق من إعدادات OpenAI'], 500);
        }
    }

    public function sendReminder(Request $request, int $hearingId): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $hearing = Hearing::with(['legalCase.client.user'])->findOrFail($hearingId);
        $client  = $hearing->legalCase?->client;

        if (! $client?->user) {
            return response()->json(['error' => 'لا يوجد حساب مرتبط بالموكل'], 422);
        }

        try {
            $client->user->notify(new AiHearingReminderNotification($hearing, $request->message));
            return response()->json(['sent' => true]);
        } catch (\Throwable $e) {
            Log::error('AI reminder send failed', ['hearing_id' => $hearingId, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'فشل إرسال التذكير'], 500);
        }
    }
}
