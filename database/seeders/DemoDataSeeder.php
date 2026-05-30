<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\EnforcementFile;
use App\Models\Expense;
use App\Models\Hearing;
use App\Models\Invoice;
use App\Models\LegalCase;
use App\Models\Office;
use App\Models\Payment;
use App\Models\PowerOfAttorney;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $office = $this->createOffice();
            $users  = $this->createUsers($office);
            $clients = $this->createClients($office, $users['admin']);
            $cases   = $this->createCases($office, $clients, $users);
            $this->createHearings($office, $cases, $users['admin']);
            $this->createExpenses($office, $cases, $users['admin']);
            $this->createPaymentsAndInvoices($office, $cases, $clients, $users['admin']);
            $this->createEnforcementFiles($office, $cases, $users['admin']);
        });
    }

    private function createOffice(): Office
    {
        return Office::create([
            'name'     => ['ar' => 'مكتب عامر للمحاماة', 'en' => 'Amer Law Office'],
            'slug'     => 'amer',
            'phone'    => '+20 127 496 9862',
            'email'    => 'amerm5798@gmail.com',
            'address'  => ['street' => 'الزقازيق', 'city' => 'الشرقية', 'country' => 'مصر'],
            'settings' => [
                'hero' => [
                    'image_path'        => '/images/hero-default.webp',
                    'heading_ar'        => 'نُحقِّق العدالة بكل احترافية',
                    'heading_en'        => 'Justice Delivered with Excellence',
                    'subtitle_ar'       => 'مكتب عامر للمحاماة — فريق من أمهر المحامين يقدم خدمات قانونية متكاملة في القضايا المدنية والتجارية والجنائية وقضايا الأسرة',
                    'subtitle_en'       => 'Amer Law Office — expert lawyers delivering comprehensive legal services in civil, commercial, criminal, and family law',
                    'founded_year'      => '1995',
                    'stat_cases'        => 500,
                    'stat_years'        => 25,
                    'stat_satisfaction' => 98,
                ],
                'contact' => [
                    'phone'            => '+20 127 496 9862',
                    'phone2'           => '+20 100 954 5140',
                    'email'            => 'amerm5798@gmail.com',
                    'whatsapp'         => '201274969862',
                    'address_ar'       => 'مصر — الشرقية — الزقازيق',
                    'address_en'       => 'Egypt — El-Sharqia — Zagazig',
                    'working_hours_ar' => 'الأحد — الخميس: ٩ ص — ٥ م',
                    'working_hours_en' => 'Sunday — Thursday: 9 AM — 5 PM',
                    'facebook'         => null,
                    'twitter_x'        => null,
                    'instagram'        => null,
                    'linkedin'         => null,
                    'youtube'          => null,
                    'tiktok'           => null,
                ],
                'seo' => [
                    'meta_title'       => 'مكتب عامر للمحاماة',
                    'meta_description' => 'مكتب عامر للمحاماة — خدمات قانونية متكاملة في مصر | الشرقية — الزقازيق',
                    'meta_keywords'    => 'محامي، مكتب محاماة، قانون، قضايا، مصر، الشرقية، الزقازيق',
                    'og_image_path'    => null,
                ],
            ],
            'is_active'=> true,
        ]);
    }

    private function createUsers(Office $office): array
    {
        $super = User::create([
            'name'      => 'Super Admin',
            'email'     => 'super@amer.test',
            'password'  => Hash::make('password'),
            'office_id' => $office->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $super->assignRole('super_admin');

        $admin = User::create([
            'name'      => 'مدير المكتب',
            'email'     => 'admin@amer.test',
            'password'  => Hash::make('password'),
            'office_id' => $office->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('office_admin');

        $lawyer1 = User::create([
            'name'      => 'المحامي أحمد',
            'email'     => 'lawyer1@amer.test',
            'password'  => Hash::make('password'),
            'office_id' => $office->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $lawyer1->assignRole('lawyer');

        $assistant = User::create([
            'name'      => 'المساعد محمد',
            'email'     => 'assistant@amer.test',
            'password'  => Hash::make('password'),
            'office_id' => $office->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $assistant->assignRole('assistant');

        $client = User::create([
            'name'      => 'عميل - خالد',
            'email'     => 'client@amer.test',
            'password'  => Hash::make('password'),
            'office_id' => $office->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $client->assignRole('client');

        return compact('super', 'admin', 'lawyer1', 'assistant', 'client');
    }

    private function createClients(Office $office, User $creator): array
    {
        $clientsData = [
            ['name' => ['ar' => 'خالد عبدالرحمن', 'en' => 'Khalid Abdulrahman'], 'type' => 'individual', 'phone' => '+966501234567', 'email' => 'khalid@example.com'],
            ['name' => ['ar' => 'شركة النور للتجارة', 'en' => 'Al-Noor Trading Co.'], 'type' => 'company', 'phone' => '+966112345678', 'email' => 'info@alnoor.com'],
            ['name' => ['ar' => 'فاطمة محمد علي', 'en' => 'Fatima Mohammed Ali'], 'type' => 'individual', 'phone' => '+966559876543', 'email' => 'fatima@example.com'],
            ['name' => ['ar' => 'مجموعة الأمل الاستثمارية', 'en' => 'Al-Amal Investment Group'], 'type' => 'company', 'phone' => '+966118765432', 'email' => 'contact@alamal.com'],
            ['name' => ['ar' => 'عبدالله سالم', 'en' => 'Abdullah Salem'], 'type' => 'individual', 'phone' => '+966533445566', 'email' => 'abdullah@example.com'],
        ];

        $clients = [];
        foreach ($clientsData as $data) {
            $clients[] = Client::create(array_merge($data, [
                'office_id'  => $office->id,
                'created_by' => $creator->id,
                'is_active'  => true,
                'id_number'  => 'ID-' . rand(1000000000, 9999999999),
                'address'    => ['city' => 'الرياض', 'country' => 'السعودية'],
            ]));
        }

        // Link the portal client user (client@amer.test) to "خالد" client record
        $clientUser = \App\Models\User::where('email', 'client@amer.test')->first();
        if ($clientUser) {
            $clients[0]->update(['user_id' => $clientUser->id]);
        }

        return $clients;
    }

    private function createCases(Office $office, array $clients, array $users): array
    {
        $casesData = [
            ['type' => 'civil', 'status' => 'active', 'court' => 'المحكمة المدنية بالرياض', 'title' => ['ar' => 'قضية استرداد الديون', 'en' => 'Debt Recovery Case'], 'client_idx' => 0],
            ['type' => 'commercial', 'status' => 'active', 'court' => 'المحكمة التجارية بالرياض', 'title' => ['ar' => 'نزاع تجاري بين الشركاء', 'en' => 'Commercial Partnership Dispute'], 'client_idx' => 1],
            ['type' => 'criminal', 'status' => 'pending', 'court' => 'المحكمة الجزائية', 'title' => ['ar' => 'قضية احتيال تجاري', 'en' => 'Commercial Fraud Case'], 'client_idx' => 2],
            ['type' => 'family', 'status' => 'active', 'court' => 'محكمة الأحوال الشخصية', 'title' => ['ar' => 'قضية حضانة أطفال', 'en' => 'Child Custody Case'], 'client_idx' => 2],
            ['type' => 'administrative', 'status' => 'adjourned', 'court' => 'المحكمة الإدارية', 'title' => ['ar' => 'طعن في قرار إداري', 'en' => 'Administrative Decision Appeal'], 'client_idx' => 3],
            ['type' => 'labor', 'status' => 'active', 'court' => 'المحكمة العمالية', 'title' => ['ar' => 'مطالبة بحقوق عمالية', 'en' => 'Labor Rights Claim'], 'client_idx' => 4],
            ['type' => 'real_estate', 'status' => 'closed', 'court' => 'المحكمة العقارية', 'title' => ['ar' => 'نزاع على ملكية عقار', 'en' => 'Property Ownership Dispute'], 'client_idx' => 0, 'closed_at' => now()->subMonths(2)],
            ['type' => 'civil', 'status' => 'archived', 'court' => 'المحكمة المدنية بجدة', 'title' => ['ar' => 'دعوى تعويض عن ضرر', 'en' => 'Compensation for Damages'], 'client_idx' => 1, 'closed_at' => now()->subYear()],
            ['type' => 'commercial', 'status' => 'pending', 'court' => 'المحكمة التجارية بجدة', 'title' => ['ar' => 'إخلال بعقد توريد', 'en' => 'Supply Contract Breach'], 'client_idx' => 3],
            ['type' => 'criminal', 'status' => 'active', 'court' => 'محكمة الاستئناف', 'title' => ['ar' => 'استئناف حكم جزائي', 'en' => 'Criminal Judgment Appeal'], 'client_idx' => 4],
        ];

        $cases = [];
        foreach ($casesData as $i => $data) {
            $case = LegalCase::create([
                'office_id'   => $office->id,
                'case_number' => 'CASE-' . now()->format('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'type'        => $data['type'],
                'status'      => $data['status'],
                'court'       => $data['court'],
                'title'       => $data['title'],
                'description' => ['ar' => 'وصف تفصيلي للقضية رقم ' . ($i + 1), 'en' => 'Detailed description for case ' . ($i + 1)],
                'client_id'   => $clients[$data['client_idx']]->id,
                'created_by'  => $users['admin']->id,
                'closed_at'   => $data['closed_at'] ?? null,
            ]);

            // Attach lawyer to case
            $case->lawyers()->attach($users['lawyer1']->id, ['role' => 'lead']);

            $cases[] = $case;
        }

        return $cases;
    }

    private function createHearings(Office $office, array $cases, User $creator): void
    {
        $hearingsData = [
            ['case_idx' => 0, 'days_from_now' => 7,   'status' => 'scheduled', 'location' => 'المحكمة المدنية — قاعة 3'],
            ['case_idx' => 1, 'days_from_now' => 14,  'status' => 'scheduled', 'location' => 'المحكمة التجارية — قاعة 1'],
            ['case_idx' => 2, 'days_from_now' => 21,  'status' => 'scheduled', 'location' => 'المحكمة الجزائية — قاعة 5'],
            ['case_idx' => 3, 'days_from_now' => 3,   'status' => 'scheduled', 'location' => 'محكمة الأحوال — قاعة 2'],
            ['case_idx' => 0, 'days_from_now' => -30, 'status' => 'held',      'location' => 'المحكمة المدنية — قاعة 3'],
            ['case_idx' => 1, 'days_from_now' => -14, 'status' => 'adjourned', 'location' => 'المحكمة التجارية — قاعة 1'],
        ];

        foreach ($hearingsData as $data) {
            Hearing::create([
                'office_id'    => $office->id,
                'case_id'      => $cases[$data['case_idx']]->id,
                'scheduled_at' => now()->addDays($data['days_from_now']),
                'location'     => $data['location'],
                'court_room'   => 'قاعة ' . rand(1, 10),
                'status'       => $data['status'],
                'notes'        => ['ar' => 'ملاحظات الجلسة'],
                'created_by'   => $creator->id,
            ]);
        }
    }

    private function createExpenses(Office $office, array $cases, User $creator): void
    {
        $expensesData = [
            ['case_idx' => 0, 'title' => ['ar' => 'رسوم المحكمة', 'en' => 'Court Fees'], 'amount' => 500, 'category' => 'court_fees', 'status' => 'paid'],
            ['case_idx' => 1, 'title' => ['ar' => 'أتعاب خبير', 'en' => 'Expert Fees'], 'amount' => 2000, 'category' => 'expert_fees', 'status' => 'paid'],
            ['case_idx' => 2, 'title' => ['ar' => 'مصاريف انتقال', 'en' => 'Travel Expenses'], 'amount' => 300, 'category' => 'travel', 'status' => 'pending'],
            ['case_idx' => 3, 'title' => ['ar' => 'رسوم توثيق', 'en' => 'Notary Fees'], 'amount' => 150, 'category' => 'notary', 'status' => 'paid'],
            ['case_idx' => 0, 'title' => ['ar' => 'رسوم استئناف', 'en' => 'Appeal Fees'], 'amount' => 800, 'category' => 'court_fees', 'status' => 'pending'],
        ];

        foreach ($expensesData as $data) {
            Expense::create([
                'office_id'  => $office->id,
                'case_id'    => $cases[$data['case_idx']]->id,
                'title'      => $data['title'],
                'amount'     => $data['amount'],
                'currency'   => 'SAR',
                'category'   => $data['category'],
                'status'     => $data['status'],
                'created_by' => $creator->id,
                'paid_at'    => $data['status'] === 'paid' ? now()->subDays(rand(1, 30)) : null,
            ]);
        }
    }

    private function createPaymentsAndInvoices(Office $office, array $cases, array $clients, User $creator): void
    {
        // Map each case to its correct client
        $caseClientMap = [
            0 => 0, // خالد
            1 => 1, // شركة النور
            2 => 2, // فاطمة
            3 => 2, // فاطمة
            4 => 3, // مجموعة الأمل
            5 => 4, // عبدالله
            6 => 0, // خالد (closed)
            7 => 1, // شركة النور (archived)
        ];

        $invoiceIndex = 1;
        foreach ($caseClientMap as $caseIdx => $clientIdx) {
            if (!isset($cases[$caseIdx])) continue;
            $case   = $cases[$caseIdx];
            $client = $clients[$clientIdx];
            $amount = rand(3000, 15000);
            $tax    = round($amount * 0.15, 2);
            $isPaid = in_array($case->status, ['closed', 'archived']) || $invoiceIndex <= 3;

            $invoice = Invoice::create([
                'office_id'      => $office->id,
                'client_id'      => $client->id,
                'case_id'        => $case->id,
                'invoice_number' => 'INV-' . now()->format('Y') . '-' . str_pad($invoiceIndex, 5, '0', STR_PAD_LEFT),
                'amount'         => $amount,
                'tax_amount'     => $tax,
                'total_amount'   => $amount + $tax,
                'currency'       => 'SAR',
                'status'         => $isPaid ? 'paid' : 'sent',
                'due_date'       => now()->addDays(30),
                'created_by'     => $creator->id,
                'notes'          => ['ar' => 'فاتورة أتعاب محاماة'],
            ]);

            if ($invoice->status === 'paid') {
                Payment::create([
                    'office_id'  => $office->id,
                    'case_id'    => $case->id,
                    'client_id'  => $client->id,
                    'amount'     => $invoice->total_amount,
                    'currency'   => 'SAR',
                    'method'     => 'bank_transfer',
                    'status'     => 'completed',
                    'reference'  => 'REF-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    'paid_at'    => now()->subDays(rand(1, 20)),
                    'created_by' => $creator->id,
                ]);
            }

            $invoiceIndex++;
        }
    }

    private function createEnforcementFiles(Office $office, array $cases, User $creator): void
    {
        $file = EnforcementFile::create([
            'office_id'          => $office->id,
            'file_number'        => 'ENF-' . now()->format('Y') . '-0001',
            'title'              => ['ar' => 'ملف تنفيذ حكم استرداد الديون', 'en' => 'Debt Recovery Enforcement File'],
            'debtor_name'        => ['ar' => 'شركة الديون المتعثرة', 'en' => 'Default Debts Co.'],
            'creditor_name'      => ['ar' => 'موكل المكتب', 'en' => 'Office Client'],
            'debt_amount'        => 50000,
            'currency'           => 'SAR',
            'status'             => 'active',
            'enforcement_office' => 'دائرة التنفيذ بالرياض',
            'created_by'         => $creator->id,
        ]);

        PowerOfAttorney::create([
            'office_id'           => $office->id,
            'poa_number'          => 'POA-' . now()->format('Y') . '-0001',
            'representative_name' => ['ar' => 'المحامي أحمد عبدالله', 'en' => 'Lawyer Ahmed Abdullah'],
            'type'                => 'judicial',
            'valid_from'          => now(),
            'valid_until'         => now()->addYear(),
            'status'              => 'active',
            'authorities'         => ['ar' => 'التقاضي والتمثيل أمام المحاكم وجميع الجهات الرسمية'],
            'case_id'             => $cases[0]->id,
            'enforcement_file_id' => $file->id,
            'created_by'          => $creator->id,
        ]);
    }
}
