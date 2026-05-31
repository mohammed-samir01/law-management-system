<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
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
            'slug'                  => ['required', 'string', 'max:100', 'unique:offices,slug', 'regex:/^[a-z0-9\-]+$/'],
            'phone'                 => ['required', 'string', 'max:20'],
            'email'                 => ['required', 'email', 'max:255', 'unique:offices,email'],
            'admin_name'            => ['required', 'string', 'max:255'],
            'admin_email'           => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password'        => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Everyone starts with a free 30-day trial (full features), regardless of chosen plan.
        $trialPlanId = Plan::where('slug', 'trial')->value('id') ?? $plan->id;

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

            Subscription::create([
                'office_id'     => $office->id,
                'plan_id'       => $trialPlanId,
                'status'        => 'trial',
                'billing_cycle' => session('onboarding_billing_cycle', 'monthly'),
                'trial_ends_at' => now()->addDays(30),
            ]);

            return $user;
        });

        session()->forget(['onboarding_plan_id', 'onboarding_billing_cycle']);

        Auth::login($user);

        return redirect()->route('register.success');
    }

    public function success()
    {
        return view('onboarding.success');
    }
}
