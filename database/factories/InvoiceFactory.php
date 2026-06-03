<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $amount    = $this->faker->randomFloat(2, 500, 20000);
        $taxAmount = round($amount * 0.15, 2);
        $statuses  = ['draft', 'sent', 'paid', 'overdue'];

        return [
            'office_id'      => Office::factory(),
            'client_id'      => Client::factory(),
            'case_id'        => null,
            'invoice_number' => 'INV-' . $this->faker->unique()->numerify('######'),
            'amount'         => $amount,
            'tax_amount'     => $taxAmount,
            'total_amount'   => $amount + $taxAmount,
            'currency'       => $this->faker->randomElement(['SAR', 'EGP']),
            'status'         => $this->faker->randomElement($statuses),
            'due_date'       => $this->faker->dateTimeBetween('+7 days', '+60 days'),
            'pdf_path'       => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(['status' => 'paid']);
    }

    public function overdue(): static
    {
        return $this->state([
            'status'   => 'overdue',
            'due_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
