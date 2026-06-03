<?php

namespace Database\Factories;

use App\Models\LegalCase;
use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

class HearingFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['scheduled', 'held', 'completed', 'adjourned', 'postponed'];

        return [
            'office_id'    => Office::factory(),
            'case_id'      => LegalCase::factory(),
            'scheduled_at' => $this->faker->dateTimeBetween('-3 months', '+3 months'),
            'location'     => $this->faker->randomElement(['قاعة 1', 'قاعة 2', 'قاعة 3', 'قاعة 5']),
            'court_room'   => $this->faker->numerify('قاعة ###'),
            'judge'        => 'القاضي ' . $this->faker->name(),
            'status'       => $this->faker->randomElement($statuses),
            'notes'        => ['ar' => $this->faker->sentence(), 'en' => null],
            'outcome'      => null,
            'created_by'   => null,
        ];
    }

    public function upcoming(): static
    {
        return $this->state([
            'scheduled_at' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'status'       => 'scheduled',
        ]);
    }

    public function past(): static
    {
        return $this->state([
            'scheduled_at' => $this->faker->dateTimeBetween('-60 days', '-1 day'),
            'status'       => $this->faker->randomElement(['held', 'completed', 'adjourned']),
        ]);
    }
}
