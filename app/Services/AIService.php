<?php

namespace App\Services;

use App\Models\AIResult;
use App\Models\Document;
use App\Models\LegalCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use OpenAI\Client;

class AIService
{
    private Client $client;
    private string $model;

    public function __construct()
    {
        $this->client = \OpenAI::client(\App\Models\PlatformSetting::openaiKey());
        $this->model  = \App\Models\PlatformSetting::get('ai.model', config('services.openai.model', 'gpt-4o'));
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

    public function predictOutcome(LegalCase $case, string $language = 'ar'): AIResult
    {
        $facts = implode("\n", [
            'رقم القضية: ' . $case->case_number,
            'النوع: ' . $case->type,
            'الحالة: ' . $case->status,
            'المحكمة: ' . ($case->court ?? 'غير محدد'),
            'الوصف: ' . ($case->getTranslation('description', 'ar') ?: $case->getTranslation('description', 'en') ?: 'لا يوجد'),
        ]);

        // Optional: a few office precedents (case law) as light context.
        $precedents = \App\Models\CaseLaw::withoutGlobalScopes()
            ->where('office_id', $case->office_id)
            ->latest()->limit(3)->get()
            ->map(fn ($c) => '- ' . ($c->getTranslation('summary', 'ar') ?: $c->getTranslation('summary', 'en') ?: ''))
            ->filter()->implode("\n");

        $context = $precedents ? "\n\nسوابق قضائية مشابهة من المكتب:\n{$precedents}" : '';

        $prompt = $language === 'ar'
            ? "أنت محامٍ خبير في تقييم القضايا. بناءً على وقائع القضية التالية، قدّم: 1) تقديراً لاحتمالية كسب القضية (نسبة مئوية تقريبية مع توضيح أنها تقدير استرشادي لا يُغني عن الرأي القانوني) 2) العوامل المؤثرة إيجاباً وسلباً 3) المخاطر 4) توصيات لتعزيز الموقف:\n\n{$facts}{$context}"
            : "You are an expert lawyer in case assessment. Based on the following case facts, provide: 1) an approximate win-likelihood (a rough percentage, clearly an advisory estimate, not legal advice) 2) positive and negative factors 3) risks 4) recommendations to strengthen the position:\n\n{$facts}{$context}";

        return $this->run(
            prompt: $prompt,
            resultType: 'case_prediction',
            morphable: $case,
            officeId: $case->office_id,
        );
    }

    public function draftLegalMemo(LegalCase $case, string $language = 'ar'): AIResult
    {
        $facts = implode("\n", [
            'رقم القضية: ' . $case->case_number,
            'النوع: ' . $case->type,
            'الحالة: ' . $case->status,
            'المحكمة: ' . ($case->court ?? 'غير محدد'),
            'الوصف: ' . ($case->getTranslation('description', 'ar') ?: $case->getTranslation('description', 'en') ?: 'لا يوجد'),
        ]);

        $prompt = $language === 'ar'
            ? "أنت محامٍ خبير في صياغة المذكرات القانونية. اكتب مذكرة قانونية احترافية كاملة بناءً على معطيات القضية التالية، تشمل: المقدمة، الوقائع، الأسانيد القانونية، الطلبات، والخاتمة:\n\n{$facts}"
            : "You are an expert lawyer skilled in drafting legal memoranda. Write a complete professional legal memo based on the following case details, including: introduction, facts, legal grounds, requests, and conclusion:\n\n{$facts}";

        return $this->run(
            prompt: $prompt,
            resultType: 'legal_memo',
            morphable: $case,
            officeId: $case->office_id,
        );
    }

    public function compareContracts(Document $first, Document $second, string $language = 'ar'): AIResult
    {
        $a = $this->getDocumentContent($first);
        $b = $this->getDocumentContent($second);

        $prompt = $language === 'ar'
            ? "أنت محامٍ متخصص في تحليل العقود. قارن بين العقدين التاليين وحدد: 1) الفروق الجوهرية 2) البنود الأفضل لكل طرف 3) المخاطر في كل عقد 4) التوصية النهائية:\n\n=== العقد الأول ===\n{$a}\n\n=== العقد الثاني ===\n{$b}"
            : "You are a contract analysis specialist. Compare the following two contracts and identify: 1) Key differences 2) Clauses favoring each party 3) Risks in each 4) Final recommendation:\n\n=== Contract A ===\n{$a}\n\n=== Contract B ===\n{$b}";

        return $this->run(
            prompt: $prompt,
            resultType: 'contract_comparison',
            morphable: $first,
            officeId: $first->office_id,
        );
    }

    private function run(string $prompt, string $resultType, Model $morphable, int $officeId): AIResult
    {
        // Final safety guard — enforce plan AI-enabled + monthly quota even if
        // a trigger bypassed the UI check. Records a failed result on breach.
        $office = \App\Models\Office::withoutGlobalScopes()->find($officeId);
        if ($office) {
            try {
                app(AIUsageService::class)->assertAllowed($office);
            } catch (\Throwable $e) {
                return AIResult::create([
                    'office_id'   => $officeId,
                    'model_type'  => get_class($morphable),
                    'model_id'    => $morphable->getKey(),
                    'result_type' => $resultType,
                    'content'     => '⚠️ ' . $e->getMessage(),
                    'model_used'  => $this->model,
                    'tokens_used' => 0,
                    'created_by'  => auth()->id(),
                ]);
            }
        }

        try {
            $response = $this->client->chat()->create([
                'model'    => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'أنت مساعد قانوني متخصص يعمل لدى مكتب محاماة. اجب بدقة ومهنية.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens'  => (int) \App\Models\PlatformSetting::get('ai.max_tokens', 2000),
                'temperature' => (float) \App\Models\PlatformSetting::get('ai.temperature', 0.3),
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
        } catch (\Throwable $e) {
            Log::error('AI request failed', ['result_type' => $resultType, 'error' => $e->getMessage()]);

            $friendly = match(true) {
                str_contains($e->getMessage(), 'API key')      => 'مفتاح OpenAI API غير صحيح أو غير مفعّل.',
                str_contains($e->getMessage(), 'quota')        => 'تم استنفاد حصة OpenAI API.',
                str_contains($e->getMessage(), 'model')        => 'النموذج المطلوب غير متاح.',
                str_contains($e->getMessage(), 'connect')      => 'تعذّر الاتصال بخادم OpenAI.',
                default                                        => 'خطأ في الذكاء الاصطناعي: ' . $e->getMessage(),
            };

            return AIResult::create([
                'office_id'   => $officeId,
                'model_type'  => get_class($morphable),
                'model_id'    => $morphable->getKey(),
                'result_type' => $resultType,
                'content'     => '⚠️ ' . $friendly,
                'model_used'  => $this->model,
                'tokens_used' => 0,
                'created_by'  => auth()->id(),
            ]);
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
