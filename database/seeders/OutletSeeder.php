<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Outlet;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = [
            [
                'name' => 'Sushi Mentai Kelapa Gading',
                'code' => 'SMT-KG',
                'address' => 'Kelapa Gading, Jakarta Utara',
                'is_active' => true,
            ],
            [
                'name' => 'Sushi Mentai Grand Indonesia',
                'code' => 'SMT-GI',
                'address' => 'Grand Indonesia Mall, Jakarta Pusat',
                'is_active' => true,
            ],
            [
                'name' => 'Sushi Mentai PIK',
                'code' => 'SMT-PIK',
                'address' => 'Pantai Indah Kapuk, Jakarta Utara',
                'is_active' => true,
            ],
            [
                'name' => 'Sushi Mentai Pondok Indah',
                'code' => 'SMT-PI',
                'address' => 'Pondok Indah Mall, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'name' => 'Sushi Mentai Central Park',
                'code' => 'SMT-CP',
                'address' => 'Central Park Mall, Jakarta Barat',
                'is_active' => true,
            ],
        ];

        foreach ($outlets as $outlet) {
            Outlet::create($outlet);
        }

        $this->command->info('Outlets seeded successfully!');
    }
}