<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlanFactory extends Factory
{
    public function definition(): array
    {
        $names = ['الأساسية', 'الاحترافية', 'المؤسسية'];
        $name  = $this->faker->unique()->randomElement($names);

        return [
            'name'          => ['ar' => $name, 'en' => 'Plan'],
            'slug'          => Str::slug($name . '-' . $this->faker->numerify('##')),
            'price_monthly' => $this->faker->randomElement([99, 199, 399]),
            'price_yearly'  => $this->faker->randomElement([990, 1990, 3990]),
            'trial_days'    => 30,
            'features'      => [],
            'is_active'     => true,
            'sort_order'    => $this->faker->numberBetween(1, 10),
        ];
    }
}
