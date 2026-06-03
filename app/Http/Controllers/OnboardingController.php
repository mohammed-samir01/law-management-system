<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\WelcomeOfficeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function showPlans()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('onboarding.plans', compact('plans'));
    }

    public function selectPlan(Request $request)
    {
        $validated = $request->validate([
            'plan_id'       => ['required', 'exists:plans,id'],
            'billing_cycle' => ['nullable', Rule::in(['monthly', 'yearly'])],
        ]);

        session([
            'onboarding_plan_id'       => $validated['plan_id'],
            'onboarding_billing_cycle' => $validated['billing_cycle'] ?? 'monthly',
        ]);

        return redirect()->route('register.setup');
    }

    public function showSetup()
    {
        $planId = session('onboarding_plan_id');

        if (! $planId) {
            return redirect()->route('register.plans');
        }

        $plan = Plan::findOrFail($planId);

        return view('onboarding.setup', compact('plan'));
    }

    public function register(Request $request)
    {
        $planId = session('onboarding_plan_id');

        if (! $planId) {
            return redirect()->route('register.plans');
        }

        $plan = Plan::findOrFail($planId);

        $validated = $request->validate([
            'office_name_ar'        => ['required', 'string', 'max:255'],
            'office_name_en'        => ['nullable', 'string', 'max:255'],
            'slug'                  => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/', Rule::unique('offices', 'slug')->whereNull('deleted_at')],
            'phone'                 => ['required', 'string', 'max:20'],
            'email'                 => ['required', 'email', 'max:255', Rule::unique('offices', 'email')->whereNull('deleted_at')],
            'admin_name'            => ['required', 'string', 'max:255'],
            'admin_email'           => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password'        => ['required', 'string', 'min:8', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        // Everyone starts with a free 30-day trial (full features), regardless of chosen plan.
        $trialPlanId = Plan::where('slug', 'trial')->value('id') ?? $plan->id;

        // Permanently remove any soft-deleted office that conflicts with this slug or email
        // so the unique DB index doesn't block re-registration.
        Office::withTrashed()
            ->where(fn ($q) => $q->where('slug', $validated['slug'])->orWhere('email', $validated['email']))
            ->whereNotNull('deleted_at')
            ->forceDelete();

        $user = DB::transaction(function () use ($validated, $trialPlanId) {
            $office = Office::create([
                'name'      => ['ar' => $validated['office_name_ar'], 'en' => $validated['office_name_en'] ?? ''],
                'slug'      => $validated['slug'],
                'phone'     => $validated['phone'],
                'email'     => $validated['email'],
                'is_active' => true,
            ]);

            $user = User::create([
                'name'      => $validated['admin_name'],
                'email'     => $validated['admin_email'],
                'password'  => Hash::make($validated['admin_password']),
                'office_id' => $office->id,
            ]);
            $user->assignRole('office_admin');

            $subscription = Subscription::create([
                'office_id'     => $office->id,
                'plan_id'       => $trialPlanId,
                'status'        => 'trial',
                'billing_cycle' => session('onboarding_billing_cycle', 'monthly'),
                'trial_ends_at' => now()->addDays(30),
            ]);

            $user->notify(new WelcomeOfficeNotification($subscription));

            return $user;
        });

        session()->forget(['onboarding_plan_id', 'onboarding_billing_cycle']);

        Auth::login($user);

        session(['needs_profile_setup' => true]);

        // If email verification is enabled, send OTP and route to verification.
        if (\App\Models\PlatformSetting::get('security.email_verification_enabled', false)) {
            app(\App\Services\EmailVerificationService::class)->sendCode($user);

            return redirect()->route('verification.notice');
        }

        return redirect()->route('register.profile');
    }

    public function showProfileSetup()
    {
        $office = auth()->user()->office;

        return view('onboarding.profile-setup', compact('office'));
    }

    public function saveProfileSetup(Request $request)
    {
        $validated = $request->validate([
            'office_name_ar'   => ['required', 'string', 'max:255'],
            'office_name_en'   => ['nullable', 'string', 'max:255'],
            'phone'            => ['required', 'string', 'max:20'],
            'phone2'           => ['nullable', 'string', 'max:20'],
            'whatsapp'         => ['nullable', 'string', 'max:20'],
            'email'            => ['required', 'email', 'max:255'],
            'address_ar'       => ['nullable', 'string', 'max:255'],
            'founded_year'     => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'working_hours_ar' => ['nullable', 'string', 'max:100'],
        ]);

        $office = auth()->user()->office;

        $office->update([
            'name'  => ['ar' => $validated['office_name_ar'], 'en' => $validated['office_name_en'] ?? ''],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
        ]);

        $currentSettings = $office->settings ?? [];

        $office->update([
            'settings' => array_replace_recursive($currentSettings, [
                'branding' => [
                    'name_ar' => $validated['office_name_ar'],
                    'name_en' => $validated['office_name_en'] ?: $validated['office_name_ar'],
                ],
                'contact' => array_filter([
                    'phone'            => $validated['phone'],
                    'phone2'           => $validated['phone2'] ?? null,
                    'whatsapp'         => $validated['whatsapp'] ?? null,
                    'email'            => $validated['email'],
                    'address_ar'       => $validated['address_ar'] ?? null,
                    'working_hours_ar' => $validated['working_hours_ar'] ?? null,
                ], fn ($v) => $v !== null && $v !== ''),
                'hero' => array_filter([
                    'founded_year' => $validated['founded_year'] ?? null,
                ], fn ($v) => $v !== null && $v !== ''),
            ]),
        ]);

        return redirect()->route('register.success');
    }

    public function success()
    {
        $office = auth()->user()?->office;

        return view('onboarding.success', compact('office'));
    }
}
