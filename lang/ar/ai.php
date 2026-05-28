<?php

return [
    'ai_assistant'        => 'المساعد الذكي',
    'ai_results'          => 'نتائج الذكاء الاصطناعي',
    'ai_analysis'         => 'تحليل الذكاء الاصطناعي',
    'new_request'         => 'طلب تحليل جديد',
    'action'              => 'نوع التحليل',
    'language'            => 'لغة النتيجة',
    'document'            => 'الوثيقة',
    'case'                => 'القضية',
    'result_details'      => 'تفاصيل النتيجة',
    'result_content'      => 'محتوى النتيجة',
    'request_queued'      => 'تم إرسال الطلب',
    'request_queued_body' => 'جارٍ معالجة الطلب في الخلفية، ستظهر النتيجة خلال لحظات.',
    'no_subject_selected' => 'لم يتم تحديد وثيقة أو قضية.',
    'analyze_document'    => 'تحليل الوثيقة',
    'summarize_case'      => 'تلخيص القضية',
    'analyze_contract'    => 'تحليل العقد',
    'processing'          => 'جارٍ التحليل...',
    'result'              => 'النتيجة',
    'model_used'          => 'النموذج المستخدم',
    'tokens_used'         => 'الرموز المستخدمة',
    'created_at'          => 'تاريخ التحليل',
    'no_results'          => 'لا توجد نتائج بعد.',

    'result_types' => [
        'document_summary'   => 'ملخص وثيقة',
        'contract_analysis'  => 'تحليل عقد',
        'case_summary'       => 'ملخص قضية',
        'legal_research'     => 'بحث قانوني',
    ],

    'actions' => [
        'summarize_case'      => 'تلخيص القضية بالذكاء الاصطناعي',
        'suggest_strategy'    => 'اقتراح استراتيجية قانونية',
        'analyze_document'    => 'تحليل وثيقة',
        'analyze_contract'    => 'تحليل عقد',
        'summarize_document'  => 'تلخيص وثيقة',
    ],

    'prompts' => [
        'document_summary'  => 'لخّص هذه الوثيقة القانونية بشكل مختصر وواضح باللغة العربية، مع ذكر أهم البنود والنقاط الجوهرية.',
        'contract_analysis' => 'حلّل هذا العقد من الناحية القانونية باللغة العربية، واذكر: الأطراف، الالتزامات، المخاطر، والملاحظات المهمة.',
        'case_summary'      => 'قدّم ملخصاً قانونياً شاملاً لهذه القضية باللغة العربية يشمل: الوقائع، الإجراءات، الجلسات، والتوصيات.',
    ],
];
