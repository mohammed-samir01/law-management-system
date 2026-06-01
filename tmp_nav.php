<?php
$all = [
  'LegalCaseResource','HearingResource','EnforcementFileResource','PowerOfAttorneyResource',
  'LegislationResource','CaseLawResource','ClientResource','InvoiceResource','ExpenseResource',
  'PaymentResource','DocumentResource','DocumentTemplateResource','AIResultResource',
  'PaymentGatewayResource','SupportTicketResource',
  'OfficeResource','UserResource','PlanResource','SubscriptionResource','PlatformLeadResource',
];
foreach (['super_admin','office_admin'] as $role) {
    $u = \App\Models\User::whereHas('roles', fn($q)=>$q->where('name',$role))->first();
    auth()->logout(); auth()->login($u);
    echo "===== $role =====\n";
    foreach ($all as $r) {
        $c = "App\Filament\Resources\$r";
        if ($c::shouldRegisterNavigation()) echo "  visible: $r\n";
    }
}
