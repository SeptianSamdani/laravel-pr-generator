<?php

namespace Database\Factories;

use App\Models\PurchaseRequisition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseRequisition>
 */
class PurchaseRequisitionFactory extends Factory
{
    protected $model = PurchaseRequisition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tanggal'    => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'perihal'    => $this->faker->sentence(4),
            'alasan'     => $this->faker->sentence(8),
            'total'      => $this->faker->randomFloat(2, 100000, 10000000),
            'status'     => 'draft',
            // created_by & outlet_id wajib diisi saat create()
        ];
    }

    /** PR dalam status submitted */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
        ]);
    }

    /** PR dalam status approved */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /** PR dalam status rejected */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /** PR dalam status paid */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'          => 'paid',
            'payment_date'    => now()->format('Y-m-d'),
            'payment_amount'  => $attributes['total'] ?? 500000,
            'payment_bank'    => 'BCA',
            'payment_account_number' => '1234567890',
            'payment_account_name'   => $this->faker->name(),
        ]);
    }
}
