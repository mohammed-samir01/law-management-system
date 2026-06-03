<?php

namespace App\Filament\Pages;

use App\Models\Addon;
use App\Models\OfficeAddon;
use App\Services\Billing\AddonBillingService;
use App\Services\Billing\PlatformBillingService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AddonsMarketplacePage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'الإضافات';
    protected static ?string $title           = 'متجر الإضافات';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.pages.addons-marketplace';

    // Livewire property — synced with the monthly/yearly toggle in the view
    public string $billingCycle = 'monthly';

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('office_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('office_admin') ?? false;
    }

    public function getAddons(): array
    {
        // NOTE: addons whose category is missing here are silently dropped from the
        // marketplace. Keep this map in sync with every seeded Addon category.
        $categories = [
            'communication' => 'التواصل',
            'legal'         => 'قانونية',
            'ai'            => 'ذكاء اصطناعي',
            'client'        => 'العملاء',
            'analytics'     => 'تحليلات',
            'productivity'  => 'الإنتاجية',
            'finance'       => 'المالية',
            'general'       => 'عام',
        ];

        $office = Auth::user()?->office;
        $addons = Addon::where('is_active', true)->orderBy('sort_order')->get();

        // Load active addons once to avoid N+1
        $activeAddonIds = $office
            ? OfficeAddon::where('office_id', $office->id)
                ->where('status', 'active')
                ->get()
                ->keyBy('addon_id')
            : collect();

        $grouped = [];

        foreach ($categories as $key => $label) {
            $items = $addons->where('category', $key)->values();
            if ($items->isEmpty()) continue;

            $grouped[] = [
                'key'    => $key,
                'label'  => $label,
                'addons' => $items->map(function ($addon) use ($activeAddonIds) {
                    $officeAddon = $activeAddonIds->get($addon->id);

                    return [
                        'id'              => $addon->id,
                        'slug'            => $addon->slug,
                        'name_ar'         => $addon->getTranslation('name', 'ar'),
                        'desc_ar'         => $addon->getTranslation('description', 'ar'),
                        'icon'            => $addon->icon,
                        'price_monthly'   => $addon->price_monthly,
                        'price_yearly'    => $addon->price_yearly,
                        'currency'        => $addon->currency,
                        'is_active'       => $officeAddon?->isActive() ?? false,
                        'expires_at'      => $officeAddon?->expires_at?->format('Y/m/d'),
                        'office_addon_id' => $officeAddon?->id,
                    ];
                })->all(),
            ];
        }

        return $grouped;
    }

    /**
     * Called from the view via wire:click="activateAddon(id)"
     * billingCycle is already synced via wire:model on the toggle.
     */
    public function activateAddon(int $addonId): void
    {
        $office = Auth::user()?->office;
        $addon  = Addon::find($addonId);

        if (! $office || ! $addon || ! $addon->is_active) {
            Notification::make()->title('الإضافة غير متاحة')->danger()->send();
            return;
        }

        if ($office->hasAddon($addon->slug)) {
            Notification::make()->title('الإضافة مفعّلة بالفعل')->warning()->send();
            return;
        }

        if (! PlatformBillingService::isConfigured()) {
            Notification::make()
                ->title('بوابة الدفع غير مُعدَّة')
                ->body('تواصل مع مسؤول النظام لإعداد بوابة الدفع.')
                ->danger()
                ->send();
            return;
        }

        try {
            $outcome = AddonBillingService::checkout($office, $addon, $this->billingCycle);
            $result  = $outcome['result'];
            $payment = $outcome['payment'];

            // Gateway requires redirect to payment page
            if (! empty($result['data']['payment_url'])) {
                $this->redirect($result['data']['payment_url']);
                return;
            }

            // Direct success (e.g. free addon or test mode)
            if ($result['success'] ?? false) {
                AddonBillingService::activate($payment, $result['data'] ?? []);
                Notification::make()
                    ->title('تم تفعيل ' . $addon->getTranslation('name', 'ar') . ' ✓')
                    ->success()
                    ->send();
                $this->redirect(static::getUrl());
                return;
            }

            $payment->update(['status' => 'failed']);
            Notification::make()
                ->title('فشلت عملية الدفع')
                ->body($result['message'] ?? 'حاول مرة أخرى أو تواصل مع الدعم.')
                ->danger()
                ->send();

        } catch (\Throwable $e) {
            Log::error('Addon activation error', ['addon_id' => $addonId, 'error' => $e->getMessage()]);
            Notification::make()
                ->title('حدث خطأ')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function cancelAddon(int $officeAddonId): void
    {
        $officeAddon = OfficeAddon::find($officeAddonId);

        if (! $officeAddon || $officeAddon->office_id !== Auth::user()?->office_id) {
            Notification::make()->title('غير مصرح')->danger()->send();
            return;
        }

        $officeAddon->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        Notification::make()
            ->title('تم إلغاء الاشتراك')
            ->body('ستستمر الإضافة في العمل حتى تاريخ انتهاء الفترة الحالية.')
            ->success()
            ->send();

        $this->redirect(static::getUrl());
    }
}
