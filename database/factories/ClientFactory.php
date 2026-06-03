<?php

namespace Database\Factories;

use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        $type   = $this->faker->randomElement(['individual', 'company']);
        $nameAr = $type === 'individual'
            ? $this->faker->name()
            : $this->faker->company() . ' للتجارة';

        return [
            'office_id'  => Office::factory(),
            'type'       => $type,
            'name'       => ['ar' => $nameAr, 'en' => $this->faker->name()],
            'id_number'  => $this->faker->numerify('1##########'),
            'phone'      => $this->faker->numerify('+966 5## ### ####'),
            'email'      => $this->faker->safeEmail(),
            'address'    => ['city' => $this->faker->city(), 'country' => 'SA'],
            'is_active'  => true,
            'created_by' => null,
        ];
    }
}
