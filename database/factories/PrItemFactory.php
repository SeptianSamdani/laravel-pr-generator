<?php

namespace Database\Factories;

use App\Models\PrItem;
use App\Models\PurchaseRequisition;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrItemFactory extends Factory
{
    protected $model = PrItem::class;

    public function definition(): array
    {
        $jumlah = $this->faker->numberBetween(1, 10);
        $harga  = $this->faker->numberBetween(50000, 2000000);

        return [
            'purchase_requisition_id' => PurchaseRequisition::factory(),
            'order'                   => 1,
            'nama_item'               => $this->faker->words(3, true),
            'jumlah'                  => $jumlah,
            'satuan'                  => $this->faker->randomElement(['pcs', 'buah', 'unit', 'kg', 'liter']),
            'harga'                   => $harga,
            'subtotal'                => $jumlah * $harga,
        ];
    }
}