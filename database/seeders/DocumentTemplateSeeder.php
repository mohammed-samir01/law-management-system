<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use App\Models\Office;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $office = Office::first();
        if (! $office) return;

        $templates = [
            [
                'name'     => ['ar' => 'عقد وكالة قانونية', 'en' => 'Power of Attorney Contract'],
                'category' => 'legal',
                'content'  => "بسم الله الرحمن الرحيم\n\nعقد وكالة قانونية\n\nإنه في يوم {{date}} الموافق {{hijri_date}}\n\nتحرر هذا العقد بين كل من:\n\nأولاً: الموكِّل\nالاسم: {{client_name}}\nرقم الهوية: {{client_id_number}}\nالعنوان: {{client_address}}\n\nثانياً: الوكيل\nالاسم: {{lawyer_name}}\nرقم القيد: {{lawyer_bar_number}}\nمكتب المحاماة: {{office_name}}\n\nالمادة الأولى: موضوع الوكالة\nوكّل الطرف الأول الطرف الثاني في تمثيله والنيابة عنه في القضية رقم {{case_number}} أمام {{court_name}} وجميع درجات التقاضي والمحاكم.\n\nالمادة الثانية: صلاحيات الوكيل\nللوكيل كامل الصلاحيات في: المرافعة والدفاع، تقديم الطلبات والمذكرات، الطعن في الأحكام، وإجراء التسويات.\n\nالمادة الثالثة: الأتعاب\nاتفق الطرفان على أتعاب محاماة قدرها {{fee_amount}} {{currency}}.\n\nتحرر هذا العقد من نسختين بيد كل طرف نسخة.\n\nتوقيع الموكل: _________________\nتوقيع الوكيل: _________________",
                'placeholders' => [
                    ['key' => 'date',              'label' => 'تاريخ العقد',        'default' => ''],
                    ['key' => 'hijri_date',         'label' => 'التاريخ الهجري',     'default' => ''],
                    ['key' => 'client_name',        'label' => 'اسم الموكل',         'default' => ''],
                    ['key' => 'client_id_number',   'label' => 'رقم هوية الموكل',    'default' => ''],
                    ['key' => 'client_address',     'label' => 'عنوان الموكل',       'default' => ''],
                    ['key' => 'lawyer_name',        'label' => 'اسم المحامي',        'default' => ''],
                    ['key' => 'lawyer_bar_number',  'label' => 'رقم القيد بالنقابة', 'default' => ''],
                    ['key' => 'office_name',        'label' => 'اسم المكتب',         'default' => ''],
                    ['key' => 'case_number',        'label' => 'رقم القضية',         'default' => ''],
                    ['key' => 'court_name',         'label' => 'اسم المحكمة',        'default' => ''],
                    ['key' => 'fee_amount',         'label' => 'مبلغ الأتعاب',       'default' => ''],
                    ['key' => 'currency',           'label' => 'العملة',             'default' => 'جنيه مصري'],
                ],
            ],
            [
                'name'     => ['ar' => 'مذكرة دفاع', 'en' => 'Defense Memorandum'],
                'category' => 'court',
                'content'  => "بسم الله الرحمن الرحيم\n\nمحكمة {{court_name}}\nالدائرة {{chamber}}\nالقضية رقم {{case_number}} لسنة {{case_year}}\n\nمذكرة بدفاع\n{{client_name}}\n(المتهم / المدعى عليه)\n\nضد\n\n{{opponent_name}}\n(المدعي)\n\nالوقائع:\n{{case_facts}}\n\nأسباب الدفع:\nأولاً: {{defense_point_1}}\nثانياً: {{defense_point_2}}\nثالثاً: {{defense_point_3}}\n\nطلبات الدفاع:\nبناءً على ما سبق، يلتمس الدفاع من المحكمة الموقرة الحكم بـ {{requested_verdict}}\n\nوالله ولي التوفيق\n\nالمحامي: {{lawyer_name}}\nالتاريخ: {{date}}",
                'placeholders' => [
                    ['key' => 'court_name',       'label' => 'اسم المحكمة',        'default' => ''],
                    ['key' => 'chamber',          'label' => 'الدائرة',            'default' => ''],
                    ['key' => 'case_number',      'label' => 'رقم القضية',         'default' => ''],
                    ['key' => 'case_year',        'label' => 'سنة القضية',         'default' => ''],
                    ['key' => 'client_name',      'label' => 'اسم الموكل',         'default' => ''],
                    ['key' => 'opponent_name',    'label' => 'اسم الخصم',          'default' => ''],
                    ['key' => 'case_facts',       'label' => 'وقائع القضية',       'default' => ''],
                    ['key' => 'defense_point_1',  'label' => 'سبب الدفع الأول',    'default' => ''],
                    ['key' => 'defense_point_2',  'label' => 'سبب الدفع الثاني',   'default' => ''],
                    ['key' => 'defense_point_3',  'label' => 'سبب الدفع الثالث',   'default' => ''],
                    ['key' => 'requested_verdict','label' => 'الحكم المطلوب',      'default' => ''],
                    ['key' => 'lawyer_name',      'label' => 'اسم المحامي',        'default' => ''],
                    ['key' => 'date',             'label' => 'التاريخ',            'default' => ''],
                ],
            ],
            [
                'name'     => ['ar' => 'عقد اتفاقية أتعاب', 'en' => 'Legal Fees Agreement'],
                'category' => 'financial',
                'content'  => "اتفاقية أتعاب محاماة\n\nبتاريخ {{date}}\n\nبين:\nالعميل: {{client_name}}\nومكتب محاماة: {{office_name}}\n\nاتفق الطرفان على ما يلي:\n\nأولاً: نطاق الخدمة\nتولي المكتب تمثيل العميل في {{service_description}}.\n\nثانياً: الأتعاب\n- أتعاب ابتدائية: {{initial_fee}} {{currency}}\n- أتعاب عند الحكم الابتدائي: {{first_verdict_fee}} {{currency}}\n- أتعاب عند الاستئناف: {{appeal_fee}} {{currency}}\n\nثالثاً: طريقة السداد\n{{payment_method}}\n\nرابعاً: المصاريف\nتُسدَّد جميع مصاريف التقاضي من {{expense_payer}}.\n\nوقّع الطرفان على هذه الاتفاقية إقراراً بمضمونها.\n\nتوقيع العميل: _________________\nتوقيع المحامي: _________________",
                'placeholders' => [
                    ['key' => 'date',               'label' => 'التاريخ',               'default' => ''],
                    ['key' => 'client_name',         'label' => 'اسم العميل',            'default' => ''],
                    ['key' => 'office_name',         'label' => 'اسم المكتب',            'default' => ''],
                    ['key' => 'service_description', 'label' => 'وصف الخدمة القانونية', 'default' => ''],
                    ['key' => 'initial_fee',         'label' => 'الأتعاب الابتدائية',    'default' => ''],
                    ['key' => 'first_verdict_fee',   'label' => 'أتعاب الحكم الابتدائي','default' => ''],
                    ['key' => 'appeal_fee',          'label' => 'أتعاب الاستئناف',      'default' => ''],
                    ['key' => 'currency',            'label' => 'العملة',               'default' => 'جنيه مصري'],
                    ['key' => 'payment_method',      'label' => 'طريقة السداد',         'default' => 'نقداً'],
                    ['key' => 'expense_payer',       'label' => 'من يتحمل المصاريف',    'default' => 'العميل'],
                ],
            ],
            [
                'name'     => ['ar' => 'إشعار قانوني / إنذار', 'en' => 'Legal Notice'],
                'category' => 'legal',
                'content'  => "إشعار قانوني\n\nإلى السيد/ة: {{recipient_name}}\nالعنوان: {{recipient_address}}\n\nتحية طيبة وبعد،\n\nنحن مكتب {{office_name}} للمحاماة، ممثلين موكلنا السيد/ة {{client_name}}، نتوجه إليكم بهذا الإشعار القانوني للإحاطة بما يلي:\n\n{{notice_body}}\n\nوعليه، نطالبكم بـ {{demand}} خلال مدة أقصاها {{deadline}} يوماً من تاريخ استلام هذا الإشعار.\n\nوفي حال عدم الاستجابة، سيضطر موكلنا إلى اتخاذ الإجراءات القانونية اللازمة دون أي إشعار آخر.\n\nمع التحية،\n\n{{lawyer_name}}\nمحامٍ ومستشار قانوني\nمكتب {{office_name}}\nالتاريخ: {{date}}",
                'placeholders' => [
                    ['key' => 'recipient_name',    'label' => 'اسم المُرسَل إليه',   'default' => ''],
                    ['key' => 'recipient_address', 'label' => 'عنوان المُرسَل إليه', 'default' => ''],
                    ['key' => 'office_name',       'label' => 'اسم المكتب',          'default' => ''],
                    ['key' => 'client_name',       'label' => 'اسم الموكل',          'default' => ''],
                    ['key' => 'notice_body',       'label' => 'نص الإشعار',          'default' => ''],
                    ['key' => 'demand',            'label' => 'المطلب',              'default' => ''],
                    ['key' => 'deadline',          'label' => 'المهلة (أيام)',        'default' => '15'],
                    ['key' => 'lawyer_name',       'label' => 'اسم المحامي',         'default' => ''],
                    ['key' => 'date',              'label' => 'التاريخ',             'default' => ''],
                ],
            ],
            [
                'name'     => ['ar' => 'عقد إيجار', 'en' => 'Lease Agreement'],
                'category' => 'contract',
                'content'  => "عقد إيجار\n\nبتاريخ {{date}}\n\nبين:\nالمؤجر: {{landlord_name}} — رقم الهوية: {{landlord_id}}\nوالمستأجر: {{tenant_name}} — رقم الهوية: {{tenant_id}}\n\nاتفق الطرفان على تأجير العقار الموصوف بـ:\nالعنوان: {{property_address}}\nالمساحة: {{property_area}} متر مربع\n\nمدة الإيجار:\nتبدأ من {{start_date}} وتنتهي في {{end_date}}.\n\nالإيجار:\nمبلغ {{rent_amount}} {{currency}} شهرياً، يُسدَّد في {{payment_day}} من كل شهر.\n\nالتأمين:\nدفع المستأجر مبلغ {{deposit_amount}} {{currency}} تأميناً قابلاً للرد.\n\nشروط خاصة:\n{{special_conditions}}\n\nتوقيع المؤجر: _________________\nتوقيع المستأجر: _________________",
                'placeholders' => [
                    ['key' => 'date',               'label' => 'تاريخ العقد',       'default' => ''],
                    ['key' => 'landlord_name',       'label' => 'اسم المؤجر',        'default' => ''],
                    ['key' => 'landlord_id',         'label' => 'هوية المؤجر',       'default' => ''],
                    ['key' => 'tenant_name',         'label' => 'اسم المستأجر',      'default' => ''],
                    ['key' => 'tenant_id',           'label' => 'هوية المستأجر',     'default' => ''],
                    ['key' => 'property_address',    'label' => 'عنوان العقار',      'default' => ''],
                    ['key' => 'property_area',       'label' => 'مساحة العقار',      'default' => ''],
                    ['key' => 'start_date',          'label' => 'تاريخ البداية',     'default' => ''],
                    ['key' => 'end_date',            'label' => 'تاريخ النهاية',     'default' => ''],
                    ['key' => 'rent_amount',         'label' => 'مبلغ الإيجار',      'default' => ''],
                    ['key' => 'currency',            'label' => 'العملة',            'default' => 'جنيه مصري'],
                    ['key' => 'payment_day',         'label' => 'يوم السداد',        'default' => 'أول'],
                    ['key' => 'deposit_amount',      'label' => 'مبلغ التأمين',      'default' => ''],
                    ['key' => 'special_conditions',  'label' => 'شروط خاصة',        'default' => 'لا يوجد'],
                ],
            ],
        ];

        foreach ($templates as $template) {
            DocumentTemplate::firstOrCreate(
                [
                    'office_id' => $office->id,
                    'name'      => json_encode($template['name']),
                ],
                [
                    'office_id'    => $office->id,
                    'name'         => $template['name'],
                    'category'     => $template['category'],
                    'content'      => $template['content'],
                    'placeholders' => $template['placeholders'],
                    'is_active'    => true,
                ]
            );
        }
    }
}
