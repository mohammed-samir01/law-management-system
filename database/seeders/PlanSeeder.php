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
                'name'            => ['ar' => 'تجربة مجانية', 'en' => 'Free Trial'],
                'slug'            => 'trial',
                'price_monthly'   => 0,
                'price_yearly'    => 0,
                'currency'        => 'EGP',
                'max_users'       => 15,
                'max_cases'       => 200,
                'max_storage_mb'  => 10240,
                'ai_enabled'      => true,
                'max_ai_requests_monthly' => 50,
                'custom_branding' => false,
                'features'        => [
                    'كل المميزات لمدة 30 يوم',
                    'حتى 15 مستخدم و200 قضية',
                    'الذكاء الاصطناعي مفعّل',
                    'بدون بطاقة دفع',
                ],
                'is_active'       => true,
                'sort_order'      => 0,
            ],
            [
                'name'            => ['ar' => 'أساسي', 'en' => 'Basic'],
                'slug'            => 'basic',
                'price_monthly'   => 199,
                'price_yearly'    => 1990,
                'currency'        => 'EGP',
                'max_users'       => 5,
                'max_cases'       => 50,
                'max_storage_mb'  => 2048,
                'ai_enabled'      => false,
                'custom_branding' => false,
                'features'        => [
                    'حتى 5 مستخدمين',
                    'حتى 50 قضية',
                    'إدارة الجلسات والوثائق',
                    'بوابة العملاء',
                    'الفواتير والمدفوعات',
                ],
                'is_active'       => true,
                'sort_order'      => 1,
            ],
            [
                'name'            => ['ar' => 'احترافي', 'en' => 'Professional'],
                'slug'            => 'pro',
                'price_monthly'   => 499,
                'price_yearly'    => 4990,
                'currency'        => 'EGP',
                'max_users'       => 15,
                'max_cases'       => 200,
                'max_storage_mb'  => 10240,
                'ai_enabled'      => true,
                'max_ai_requests_monthly' => 300,
                'custom_branding' => false,
                'features'        => [
                    'حتى 15 مستخدم',
                    'حتى 200 قضية',
                    'كل مميزات الأساسي',
                    'الذكاء الاصطناعي (تلخيص وتحليل)',
                    'تقارير متقدمة',
                ],
                'is_active'       => true,
                'sort_order'      => 2,
            ],
            [
                'name'            => ['ar' => 'مؤسسي', 'en' => 'Enterprise'],
                'slug'            => 'enterprise',
                'price_monthly'   => 999,
                'price_yearly'    => 9990,
                'currency'        => 'EGP',
                'max_users'       => 9999,
                'max_cases'       => 9999,
                'max_storage_mb'  => 102400,
                'ai_enabled'      => true,
                'max_ai_requests_monthly' => null, // unlimited
                'custom_branding' => true,
                'features'        => [
                    'مستخدمون وقضايا بلا حدود',
                    'كل مميزات الاحترافي',
                    'علامة تجارية مخصصة',
                    'تخزين موسّع',
                    'دعم ذو أولوية',
                ],
                'is_active'       => true,
                'sort_order'      => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
