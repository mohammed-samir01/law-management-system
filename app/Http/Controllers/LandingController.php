<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LandingController extends Controller
{
    public function index()
    {
        $office   = Office::withoutGlobalScopes()->where('is_active', true)->first();
        $settings = array_replace_recursive($this->getDefaultSettings(), $office?->settings ?? []);

        return view('landing.index', compact('settings'));
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $office = Office::withoutGlobalScopes()->where('is_active', true)->first();

        if ($office) {
            SupportTicket::create([
                'office_id'     => $office->id,
                'title'         => $validated['subject'],
                'description'   => $validated['message'],
                'visitor_name'  => $validated['name'],
                'visitor_email' => $validated['email'],
                'visitor_phone' => $validated['phone'] ?? null,
                'category'      => 'general',
                'priority'      => 'normal',
                'status'        => 'open',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.contact_success'),
        ]);
    }

    public function getDefaultSettings(): array
    {
        return [
            'seo' => [
                'meta_title'       => null,
                'meta_description' => 'مكتب عامر للمحاماة — خدمات قانونية متكاملة بأعلى مستوى من الاحترافية',
                'meta_keywords'    => 'محامي، مكتب محاماة، قانون، قضايا، مصر',
                'og_image_path'    => null,
            ],
            'branding' => [
                'logo_path'     => null,
                'primary_color' => '#1E3A5F',
                'accent_color'  => '#C9A84C',
            ],
            'hero' => [
                'image_path'       => '/images/hero-default.webp',
                'heading_ar'       => 'نُحقِّق العدالة بكل احترافية',
                'heading_en'       => 'Justice Delivered with Excellence',
                'subtitle_ar'      => 'مكتب عامر للمحاماة — فريق من أمهر المحامين يقدم خدمات قانونية متكاملة في القضايا المدنية والتجارية والجنائية وقضايا الأسرة',
                'subtitle_en'      => 'Amer Law Office — a team of expert lawyers delivering comprehensive legal services in civil, commercial, criminal, and family law',
                'founded_year'     => '1995',
                'stat_cases'       => 500,
                'stat_years'       => 25,
                'stat_satisfaction'=> 98,
            ],
            'stats' => [
                ['value' => 500,  'suffix' => '+', 'label_ar' => 'قضية ناجحة',      'label_en' => 'Successful Cases'],
                ['value' => 25,   'suffix' => '+', 'label_ar' => 'سنة خبرة',         'label_en' => 'Years of Experience'],
                ['value' => 1200, 'suffix' => '+', 'label_ar' => 'عميل راضٍ',        'label_en' => 'Satisfied Clients'],
                ['value' => 98,   'suffix' => '%', 'label_ar' => 'نسبة رضا العملاء', 'label_en' => 'Client Satisfaction Rate'],
            ],
            'services' => [
                ['icon' => 'scale',     'title_ar' => 'القضايا المدنية',   'title_en' => 'Civil Litigation',   'desc_ar' => 'نتولى الدفاع عن حقوقك في النزاعات المدنية والعقود والمسؤولية التقصيرية بخبرة واسعة',                         'desc_en' => 'Expert representation in civil disputes, contracts, and tort liability cases'],
                ['icon' => 'building',  'title_ar' => 'القانون التجاري',   'title_en' => 'Commercial Law',     'desc_ar' => 'خدمات قانونية شاملة للشركات تشمل التأسيس والعقود والنزاعات التجارية والاندماج والاستحواذ',                   'desc_en' => 'Comprehensive corporate services including incorporation, contracts, disputes, M&A'],
                ['icon' => 'users',     'title_ar' => 'قضايا الأسرة',      'title_en' => 'Family Law',         'desc_ar' => 'نتعامل بحساسية وكفاءة مع قضايا الطلاق والحضانة والنفقة والميراث وتوثيق العقود الأسرية',                    'desc_en' => 'Sensitive and efficient handling of divorce, custody, alimony, inheritance, and family contracts'],
                ['icon' => 'shield',    'title_ar' => 'القضايا الجنائية',  'title_en' => 'Criminal Defense',   'desc_ar' => 'دفاع قانوني متخصص في القضايا الجنائية وضمان حقوق المتهمين في جميع مراحل التقاضي',                         'desc_en' => "Specialized criminal defense ensuring defendants' rights at all stages of proceedings"],
                ['icon' => 'briefcase', 'title_ar' => 'قانون العمل',       'title_en' => 'Labor Law',          'desc_ar' => 'نزاعات العمل وعقود التوظيف والفصل التعسفي وتسوية النزاعات بين أصحاب العمل والموظفين',                     'desc_en' => 'Labor disputes, employment contracts, wrongful termination, and employer-employee mediation'],
                ['icon' => 'home',      'title_ar' => 'العقارات والتطوير', 'title_en' => 'Real Estate Law',    'desc_ar' => 'صياغة عقود البيع والإيجار وفض النزاعات العقارية وتسجيل الملكيات وخدمات التطوير العقاري',                   'desc_en' => 'Drafting sale/lease contracts, property disputes, title registration, and real estate development'],
            ],
            'team' => [
                ['name_ar' => 'أ. محمد العبدالله', 'name_en' => 'Att. Mohammad Al-Abdullah', 'role_ar' => 'مؤسس المكتب — قانون تجاري',  'role_en' => 'Founding Partner — Commercial Law',  'bio_ar' => 'أكثر من 25 عاماً من الخبرة في القانون التجاري وقضايا الشركات. خريج جامعة القاهرة وحاصل على دكتوراه في القانون الدولي.', 'bio_en' => 'Over 25 years in commercial and corporate law. Cairo University graduate with a PhD in International Law.',                                     'initials' => 'م', 'color' => 'bg-navy'],
                ['name_ar' => 'أ. سارة الخالد',    'name_en' => 'Att. Sarah Al-Khaled',       'role_ar' => 'شريك — قانون الأسرة',        'role_en' => 'Partner — Family Law',              'bio_ar' => 'متخصصة في قضايا الأسرة والأحوال الشخصية مع سجل ممتاز في محاكم الأحوال الشخصية والاستئناف.',                         'bio_en' => 'Specialized in family and personal status law with an excellent record in family courts and appellate proceedings.',                                'initials' => 'س', 'color' => 'bg-gold'],
                ['name_ar' => 'أ. خالد المنصور',   'name_en' => 'Att. Khaled Al-Mansour',     'role_ar' => 'شريك — قضايا جنائية',        'role_en' => 'Partner — Criminal Defense',        'bio_ar' => 'محامٍ جنائي بارز بخبرة 18 عاماً في الدفاع الجنائي وحقوق الإنسان وضمان المحاكمات العادلة.',                         'bio_en' => 'Prominent criminal defense attorney with 18 years in criminal defense and human rights litigation.',                                               'initials' => 'خ', 'color' => 'bg-navy-light'],
            ],
            'testimonials' => [
                ['quote_ar' => 'الفريق القانوني في عامر أبدع في التعامل مع قضيتي التجارية المعقدة. حصلت على نتيجة أفضل مما توقعت في وقت قياسي. شكراً لأ. محمد وفريقه المتميز.',    'quote_en' => 'The legal team at Amer excelled in handling my complex commercial case. I got a better outcome than expected in record time. Thank you to Att. Mohammad and his outstanding team.',    'name_ar' => 'أحمد الصالح',   'name_en' => 'Ahmed Al-Saleh',   'role_ar' => 'رئيس تنفيذي — شركة الأفق', 'role_en' => 'CEO — Al-Ufuq Company',    'initials' => 'أ', 'rating' => 5],
                ['quote_ar' => 'تعاملت مع أ. سارة في قضية حضانة صعبة. أبدت تفهماً وكفاءة عالية، وكانت دائماً متاحة للرد على استفساراتي. عامر فعلاً مكتب محاماة استثنائي.',            'quote_en' => 'I worked with Att. Sarah on a difficult custody case. She showed great understanding and competence, always available to answer my questions. Amer is truly an exceptional law firm.', 'name_ar' => 'منال العمري',    'name_en' => 'Manal Al-Omari',   'role_ar' => 'عميلة — قضية أسرية',       'role_en' => 'Client — Family Case',     'initials' => 'م', 'rating' => 5],
                ['quote_ar' => 'عندما واجهت تهمة جنائية باطلة، لجأت إلى أ. خالد. دفاعه المحكم أدى إلى براءتي الكاملة. لا أعرف ماذا كنت سأفعل بدون عامر.',                            'quote_en' => "When I faced a false criminal charge, I turned to Att. Khaled. His solid defense led to my full acquittal. I don't know what I would have done without Amer.",                       'name_ar' => 'فيصل المطيري',   'name_en' => 'Faisal Al-Mutairi', 'role_ar' => 'عميل — قضية جنائية',       'role_en' => 'Client — Criminal Case',   'initials' => 'ف', 'rating' => 5],
                ['quote_ar' => 'بوابة العميل الإلكترونية رائعة — أتابع ملفي القانوني وأتواصل مع محامي في أي وقت. عامر يجمع بين الاحترافية التقنية والكفاءة القانونية.',                  'quote_en' => 'The client portal is amazing — I can track my legal file and communicate with my lawyer anytime. Amer combines technical excellence with legal expertise.',                              'name_ar' => 'نورة الزهراني', 'name_en' => 'Noura Al-Zahrani', 'role_ar' => 'مديرة مالية — شركة ناشئة', 'role_en' => 'CFO — Startup Company',    'initials' => 'ن', 'rating' => 5],
            ],
            'why_us' => [
                ['icon' => 'lightbulb', 'title_ar' => 'خبرة متخصصة',  'title_en' => 'Specialized Expertise',    'desc_ar' => 'فريقنا من المحامين المتخصصين يمتلك خبرة تتجاوز 25 عاماً في مختلف فروع القانون مع سجل حافل من النجاحات',                    'desc_en' => 'Our specialized legal team has over 25 years of experience across all branches of law with a proven track record'],
                ['icon' => 'lock',      'title_ar' => 'سرية تامة',     'title_en' => 'Complete Confidentiality', 'desc_ar' => 'نلتزم بأعلى معايير السرية المهنية. جميع معلوماتك وملفاتك محفوظة بحماية كاملة ولا تُكشف لأي طرف',                         'desc_en' => 'We uphold the highest professional confidentiality standards. All your information is fully protected and never disclosed'],
                ['icon' => 'clock',     'title_ar' => 'متاحون دائماً', 'title_en' => 'Always Available',         'desc_ar' => 'نوفر بوابة إلكترونية متكاملة تتيح لك متابعة قضيتك والتواصل مع فريقك القانوني في أي وقت وأي مكان',                      'desc_en' => 'Our integrated client portal lets you track your case and communicate with your legal team anytime, anywhere'],
            ],
            'contact' => [
                'phone'            => '01274969862',
                'phone2'           => '01009545140',
                'email'            => 'amerm5798@gmail.com',
                'whatsapp'         => '201274969862',
                'address_ar'       => 'القاهرة، مصر',
                'address_en'       => 'Cairo, Egypt',
                'working_hours_ar' => 'الأحد — الخميس: ٩ ص — ٥ م',
                'working_hours_en' => 'Sunday — Thursday: 9 AM — 5 PM',
                'facebook'         => null,
                'twitter_x'        => null,
                'instagram'        => null,
                'linkedin'         => null,
                'youtube'          => null,
                'tiktok'           => null,
            ],
        ];
    }
}
