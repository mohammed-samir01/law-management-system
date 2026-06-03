<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OfficeFactory extends Factory
{
    public function definition(): array
    {
        $names = [
            'مكتب النور للمحاماة', 'مكتب العدل والحق', 'مكتب الأمانة القانونية',
            'مكتب الميزان للمحاماة', 'مكتب الفاروق للاستشارات', 'مكتب الرشيد للمحاماة',
            'مكتب العقيل القانوني', 'مكتب الحكمة والعدل', 'مكتب البيان للمحاماة',
        ];
        $nameAr = $this->faker->randomElement($names);
        $slug   = Str::slug($nameAr . '-' . $this->faker->unique()->numerify('###'));

        return [
            'name'      => ['ar' => $nameAr, 'en' => 'Law Office'],
            'slug'      => $slug,
            'phone'     => $this->faker->numerify('+966 5## ### ####'),
            'email'     => $this->faker->unique()->safeEmail(),
            'address'   => ['city' => $this->faker->city(), 'country' => 'SA'],
            'is_active' => true,
            'settings'  => [],
        ];
    }
}
