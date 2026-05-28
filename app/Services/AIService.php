<?php

namespace App\Services;

use App\Models\AIResult;
use App\Models\Document;
use App\Models\LegalCase;
use Illuminate\Support\Facades\Log;
use OpenAI\Client;

class AIService
{
    private Client $client;
    private string $model = 'gpt-4o';

    public function __construct()
    {
        $this->client = \OpenAI::client(config('services.openai.api_key'));
    }

    public function summarizeDocument(Document $document, string $language = 'ar'): AIResult
    {
        $content = $this->getDocumentContent($document);

        $prompt = $language === 'ar'
            ? "أنت محامٍ خبير. قم بتلخيص الوثيقة القانونية التالية بشكل موجز ودقيق، مع إبراز النقاط الجوهرية والالتزامات والمخاطر:\n\n{$content}"
            : "You are an expert lawyer. Summarize the following legal document concisely and accurately, highlighting key points, obligations, and risks:\n\n{$content}";

        return $this->run(
            prompt: $prompt,
            resultType: 'document_summary',
            morphable: $document,
            officeId: $document->office_id,
        );
    }

    public function analyzeContract(Document $document, string $language = 'ar'): AIResult
    {
        $content = $this->getDocumentContent($document);

        $prompt = $language === 'ar'
            ? "أنت محامٍ متخصص في تحليل العقود. قم بتحليل العقد التالي وحدد: 1) الأطراف والتزاماتهم 2) البنود الحرجة أو المثيرة للقلق 3) المخاطر القانونية المحتملة 4) التوصيات:\n\n{$content}"
            : "You are a contract analysis specialist. Analyze the following contract and identify: 1) Parties and their obligations 2) Critical or concerning clauses 3) Potential legal risks 4) Recommendations:\n\n{$content}";

        return $this->run(
            prompt: $prompt,
            resultType: 'contract_analysis',
            morphable: $document,
            officeId: $document->office_id,
        );
    }

    public function summarizeCase(LegalCase $case, string $language = 'ar'): AIResult
    {
        $facts = implode("\n", [
            'رقم القضية: ' . $case->case_number,
            'النوع: ' . $case->type,
            'الحالة: ' . $case->status,
            'المحكمة: ' . ($case->court ?? 'غير محدد'),
            'الوصف: ' . ($case->getTranslation('description', 'ar') ?: $case->getTranslation('description', 'en') ?: 'لا يوجد'),
        ]);

        $prompt = $language === 'ar'
            ? "أنت محامٍ خبير. قم بإعداد ملخص تنفيذي للقضية التالية:\n\n{$facts}"
            : "You are an expert lawyer. Prepare an executive summary for the following case:\n\n{$facts}";

        return $this->run(
            prompt: $prompt,
            resultType: 'case_summary',
            morphable: $case,
            officeId: $case->office_id,
        );
    }

    public function suggestStrategy(LegalCase $case, string $language = 'ar'): AIResult
    {
        $facts = implode("\n", [
            'رقم القضية: ' . $case->case_number,
            'النوع: ' . $case->type,
            'الحالة: ' . $case->status,
            'المحكمة: ' . ($case->court ?? 'غير محدد'),
            'الوصف: ' . ($case->getTranslation('description', 'ar') ?: $case->getTranslation('description', 'en') ?: 'لا يوجد'),
        ]);

        $prompt = $language === 'ar'
            ? "أنت محامٍ استراتيجي خبير. بناءً على معطيات القضية التالية، اقترح استراتيجية قانونية مفصلة تشمل الحجج والأدلة المطلوبة والإجراءات الموصى بها:\n\n{$facts}"
            : "You are an expert legal strategist. Based on the following case details, suggest a detailed legal strategy including arguments, required evidence, and recommended procedures:\n\n{$facts}";

        return $this->run(
            prompt: $prompt,
            resultType: 'strategy_suggestion',
            morphable: $case,
            officeId: $case->office_id,
        );
    }

    private function run(string $prompt, string $resultType, Model|\Illuminate\Database\Eloquent\Model $morphable, int $officeId): AIResult
    {
        try {
            $response = $this->client->chat()->create([
                'model'    => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'أنت مساعد قانوني متخصص يعمل لدى مكتب محاماة. اجب بدقة ومهنية.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens'  => 2000,
                'temperature' => 0.3,
            ]);

            $content    = $response->choices[0]->message->content;
            $tokensUsed = $response->usage->totalTokens;

            return AIResult::create([
                'office_id'   => $officeId,
                'model_type'  => get_class($morphable),
                'model_id'    => $morphable->getKey(),
                'result_type' => $resultType,
                'content'     => $content,
                'model_used'  => $this->model,
                'tokens_used' => $tokensUsed,
                'created_by'  => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('AI request failed', [
                'result_type' => $resultType,
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function getDocumentContent(Document $document): string
    {
        if ($document->hasMedia('files')) {
            $path = $document->getFirstMedia('files')?->getPath();
            if ($path && file_exists($path)) {
                return file_get_contents($path) ?: '';
            }
        }

        return $document->getTranslation('title', 'ar') ?: $document->getTranslation('title', 'en') ?: '';
    }
}
