<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'                    => ['ar' => 'تجربة مجانية', 'en' => 'Free Trial'],
                'slug'                    => 'trial',
                'price_monthly'           => 0,
                'price_yearly'            => 0,
                'currency'                => 'EGP',
                'max_users'               => 15,
                'max_cases'               => 200,
                'max_storage_mb'          => 10240,
                'ai_enabled'              => true,
                'max_ai_requests_monthly' => 50,
                'custom_branding'         => false,
                'features'                => [
                    ['ar' => 'كل المميزات لمدة 30 يوم',        'en' => 'All features for 30 days'],
                    ['ar' => 'حتى 15 مستخدم و200 قضية',        'en' => 'Up to 15 users & 200 cases'],
                    ['ar' => 'الذكاء الاصطناعي مفعّل',          'en' => 'AI features enabled'],
                    ['ar' => 'بدون بطاقة دفع',                 'en' => 'No credit card required'],
                ],
                'is_active'  => true,
                'sort_order' => 0,
            ],
            [
                'name'                    => ['ar' => 'أساسي', 'en' => 'Basic'],
                'slug'                    => 'basic',
                'price_monthly'           => 199,
                'price_yearly'            => 1990,
                'currency'                => 'EGP',
                'max_users'               => 5,
                'max_cases'               => 50,
                'max_storage_mb'          => 2048,
                'ai_enabled'              => false,
                'max_ai_requests_monthly' => 0,
                'custom_branding'         => false,
                'features'                => [
                    ['ar' => 'حتى 5 مستخدمين',               'en' => 'Up to 5 users'],
                    ['ar' => 'حتى 50 قضية',                   'en' => 'Up to 50 cases'],
                    ['ar' => 'إدارة الجلسات والوثائق',         'en' => 'Hearings & document management'],
                    ['ar' => 'بوابة العملاء',                  'en' => 'Client portal'],
                    ['ar' => 'الفواتير والمدفوعات',             'en' => 'Invoices & payments'],
                ],
                'is_active'  => true,
                'sort_order' => 1,
            ],
            [
                'name'                    => ['ar' => 'احترافي', 'en' => 'Professional'],
                'slug'                    => 'pro',
                'price_monthly'           => 499,
                'price_yearly'            => 4990,
                'currency'                => 'EGP',
                'max_users'               => 15,
                'max_cases'               => 200,
                'max_storage_mb'          => 10240,
                'ai_enabled'              => true,
                'max_ai_requests_monthly' => 300,
                'custom_branding'         => false,
                'features'                => [
                    ['ar' => 'حتى 15 مستخدم',                          'en' => 'Up to 15 users'],
                    ['ar' => 'حتى 200 قضية',                           'en' => 'Up to 200 cases'],
                    ['ar' => 'كل مميزات الأساسي',                       'en' => 'All Basic features'],
                    ['ar' => 'الذكاء الاصطناعي (تلخيص وتحليل)',          'en' => 'AI (summarization & analysis)'],
                    ['ar' => 'تقارير متقدمة',                           'en' => 'Advanced reports'],
                ],
                'is_active'  => true,
                'sort_order' => 2,
            ],
            [
                'name'                    => ['ar' => 'مؤسسي', 'en' => 'Enterprise'],
                'slug'                    => 'enterprise',
                'price_monthly'           => 999,
                'price_yearly'            => 9990,
                'currency'                => 'EGP',
                'max_users'               => 9999,
                'max_cases'               => 9999,
                'max_storage_mb'          => 102400,
                'ai_enabled'              => true,
                'max_ai_requests_monthly' => null,
                'custom_branding'         => true,
                'features'                => [
                    ['ar' => 'مستخدمون وقضايا بلا حدود',   'en' => 'Unlimited users & cases'],
                    ['ar' => 'كل مميزات الاحترافي',          'en' => 'All Professional features'],
                    ['ar' => 'علامة تجارية مخصصة',           'en' => 'Custom branding'],
                    ['ar' => 'تخزين موسّع',                  'en' => 'Extended storage'],
                    ['ar' => 'دعم ذو أولوية',                'en' => 'Priority support'],
                ],
                'is_active'  => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
