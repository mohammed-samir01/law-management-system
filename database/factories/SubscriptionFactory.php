<?php

namespace Database\Factories;

use App\Models\Office;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'office_id'     => Office::factory(),
            'plan_id'       => Plan::factory(),
            'status'        => 'trial',
            'billing_cycle' => 'monthly',
            'trial_ends_at' => now()->addDays(30),
            'grace_ends_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state([
            'status'               => 'active',
            'trial_ends_at'        => null,
            'current_period_start' => now(),
            'current_period_end'   => now()->addMonth(),
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'status'               => 'expired',
            'trial_ends_at'        => now()->subDays(5),
            'current_period_end'   => now()->subDays(5),
            'grace_ends_at'        => null,
        ]);
    }

    public function onGrace(): static
    {
        return $this->state([
            'status'        => 'expired',
            'trial_ends_at' => now()->subDays(2),
            'grace_ends_at' => now()->addDays(3),
        ]);
    }
}
