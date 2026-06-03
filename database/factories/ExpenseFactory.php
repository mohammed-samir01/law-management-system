<?php

namespace Database\Factories;

use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        $categories = ['رسوم المحكمة', 'مواصلات', 'طباعة ونسخ', 'اتصالات', 'استشارات خارجية', 'أخرى'];

        return [
            'office_id'   => Office::factory(),
            'case_id'     => null,
            'title'       => ['ar' => $this->faker->randomElement($categories), 'en' => null],
            'category'    => $this->faker->randomElement($categories),
            'amount'      => $this->faker->randomFloat(2, 50, 5000),
            'currency'    => $this->faker->randomElement(['SAR', 'EGP']),
            'status'      => $this->faker->randomElement(['paid', 'unpaid', 'pending']),
            'receipt_path'=> null,
            'paid_at'     => null,
            'created_by'  => null,
        ];
    }
}
