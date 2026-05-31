<?php
foreach (['super_admin','office_admin','lawyer'] as $role) {
    $u = \App\Models\User::whereHas('roles', fn($q)=>$q->where('name',$role))->first();
    auth()->logout(); auth()->login($u);
    echo "$role → إعدادات النظام: " . (\App\Filament\Pages\SystemSettingsPage::canAccess() ? 'YES' : 'no') . "\n";
}
