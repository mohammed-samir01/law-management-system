<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        $row = PlatformSetting::singleton();

        // Only seed if this is a fresh install (no data yet)
        if (! empty($row->data['brand']['name_ar'])) {
            return;
        }

        $row->update([
            'data' => array_replace_recursive($row->data ?? [], [
                'brand' => [
                    'name_ar'   => 'ميزان',
                    'name_en'   => 'Mizan',
                    'logo_path' => null,
                ],
                'hero' => [
                    'heading_ar'  => 'منصة ميزان لإدارة مكاتب المحاماة',
                    'heading_en'  => 'Mizan — Law Office Management Platform',
                    'subtitle_ar' => 'نظام متكامل لإدارة القضايا والجلسات والعملاء والفواتير والوثائق — مع بوابة عملاء وذكاء اصطناعي. ابدأ تجربتك المجانية لمدة شهر كامل.',
                    'subtitle_en' => 'A complete system to manage cases, hearings, clients, invoices, and documents — with a client portal and AI. Start your free one-month trial.',
                    'cta_ar'      => 'ابدأ مجاناً',
                    'cta_en'      => 'Start Free',
                ],
                'stats' => [
                    ['value' => 5000, 'suffix' => '+', 'label_ar' => 'قضية مُدارة',   'label_en' => 'Cases Managed'],
                    ['value' => 300,  'suffix' => '+', 'label_ar' => 'مكتب محاماة',   'label_en' => 'Law Offices'],
                    ['value' => 99,   'suffix' => '%', 'label_ar' => 'رضا العملاء',   'label_en' => 'Satisfaction'],
                    ['value' => 24,   'suffix' => '/7', 'label_ar' => 'دعم متواصل',   'label_en' => 'Support'],
                ],
                'contact' => [
                    'phone'      => '+20 100 000 0000',
                    'email'      => 'info@mizan.com',
                    'whatsapp'   => '201000000000',
                    'address_ar' => 'مصر — القاهرة',
                    'address_en' => 'Egypt — Cairo',
                    'facebook'   => null,
                    'twitter_x'  => null,
                    'instagram'  => null,
                    'linkedin'   => null,
                ],
                'ai' => [
                    'model'       => 'gpt-4o',
                    'max_tokens'  => 2000,
                    'temperature' => 0.3,
                ],
                'security' => [
                    'email_verification_enabled' => false,
                    'rate' => [
                        'login'    => 5,
                        'register' => 3,
                        'contact'  => 5,
                        'otp'      => 3,
                        'uploads'  => 30,
                        'ai'       => 20,
                        'api'      => 120,
                    ],
                ],
                'otp' => [
                    'length'       => 6,
                    'ttl_minutes'  => 15,
                    'max_attempts' => 5,
                ],
                'media' => [
                    'max_upload_kb' => 10240,
                    'avatar_max_kb' => 2048,
                ],
                'developer' => [
                    'name'     => 'Mohamed Shahin',
                    'linkedin' => '',
                ],
            ]),
        ]);
    }
}
