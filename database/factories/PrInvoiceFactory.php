<?php

namespace Database\Factories;

use App\Models\PrInvoice;
use App\Models\PurchaseRequisition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrInvoiceFactory extends Factory
{
    protected $model = PrInvoice::class;

    public function definition(): array
    {
        $ext  = $this->faker->randomElement(['jpg', 'png', 'pdf']);
        $mime = $ext === 'pdf' ? 'application/pdf' : 'image/jpeg';

        return [
            'purchase_requisition_id' => PurchaseRequisition::factory(),
            'file_name'               => $this->faker->word() . '.' . $ext,
            'file_path'               => 'invoices/' . $this->faker->uuid() . '.' . $ext,
            'file_type'               => $mime,
            'file_size'               => $this->faker->numberBetween(50000, 5000000),
            'uploaded_by'             => User::factory(),
        ];
    }
}