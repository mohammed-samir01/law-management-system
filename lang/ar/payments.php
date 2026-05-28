<?php

return [
    'payments'               => 'المدفوعات',
    'payment'                => 'دفعة',
    'create'                 => 'تسجيل دفعة جديدة',
    'edit'                   => 'تعديل الدفعة',
    'amount'                 => 'المبلغ',
    'currency'               => 'العملة',
    'method'                 => 'طريقة الدفع',
    'gateway'                => 'البوابة',
    'gateway_transaction_id' => 'رقم المعاملة',
    'reference'              => 'المرجع',
    'status'                 => 'الحالة',
    'paid_at'                => 'تاريخ الدفع',
    'notes'                  => 'ملاحظات',
    'client'                 => 'العميل',
    'case'                   => 'القضية',

    'methods' => [
        'cash'          => 'نقدي',
        'bank_transfer' => 'تحويل بنكي',
        'card'          => 'بطاقة',
        'paymob'        => 'Paymob',
        'instapay'      => 'InstaPay',
        'vodafone_cash' => 'Vodafone Cash',
        'moyasar'       => 'Moyasar',
        'mada'          => 'Mada',
        'paytabs'       => 'PayTabs',
        'stripe'        => 'Stripe',
        'paypal'        => 'PayPal',
    ],
    'statuses' => [
        'pending'   => 'معلق',
        'completed' => 'مكتمل',
        'failed'    => 'فاشل',
        'refunded'  => 'مُسترد',
    ],
];
