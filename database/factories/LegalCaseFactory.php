<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegalCaseFactory extends Factory
{
    public function definition(): array
    {
        $types    = ['civil', 'criminal', 'family', 'labor', 'commercial', 'administrative', 'real_estate'];
        $statuses = ['new', 'active', 'pending', 'adjourned', 'closed'];
        $courts   = ['محكمة الاستئناف', 'المحكمة الابتدائية', 'محكمة العمل', 'المحكمة التجارية', 'محكمة الأحوال الشخصية'];

        return [
            'office_id'   => Office::factory(),
            'client_id'   => Client::factory(),
            'case_number' => $this->faker->unique()->numerify('####/####'),
            'type'        => $this->faker->randomElement($types),
            'title'       => ['ar' => 'قضية ' . $this->faker->words(3, true), 'en' => 'Case ' . $this->faker->words(2, true)],
            'description' => ['ar' => $this->faker->paragraph(), 'en' => null],
            'court'       => $this->faker->randomElement($courts),
            'judge'       => 'القاضي ' . $this->faker->name(),
            'status'      => $this->faker->randomElement($statuses),
            'created_by'  => null,
            'closed_at'   => null,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function closed(): static
    {
        return $this->state(['status' => 'closed', 'closed_at' => now()]);
    }
}
