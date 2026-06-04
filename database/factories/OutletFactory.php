<?php

namespace Database\Factories;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutletFactory extends Factory
{
    protected $model = Outlet::class;

    public function definition(): array
    {
        return [
            'name'      => $this->faker->unique()->company() . ' Outlet',
            'code'      => strtoupper($this->faker->unique()->lexify('SMT-???')),
            'address'   => $this->faker->address(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}