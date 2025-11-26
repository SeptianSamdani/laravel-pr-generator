<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequisition;
use App\Models\PrItem;
use App\Models\Outlet;
use App\Models\User;
use Carbon\Carbon;

class PurchaseRequisitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff   = User::whereHas('roles', fn($q) => $q->where('name', 'staff'))->first();
        $manager = User::whereHas('roles', fn($q) => $q->where('name', 'manager'))->first();
        $outlet  = Outlet::first();

        if (!$staff || !$manager || !$outlet) {
            $this->command->error("Harap jalankan OutletSeeder & RolePermissionSeeder terlebih dahulu.");
            return;
        }

        $dataPR = [
            [
                'tanggal' => now()->subDays(5),
                'perihal' => 'Pengadaan Bahan Sushi',
                'alasan'  => 'Untuk stok mingguan outlet',
                'outlet_id' => $outlet->id,
                'total' => 0,
                'status' => 'approved',
                'created_by' => $staff->id,
                'approved_by' => $manager->id,
                'approved_at' => now()->subDays(4),
                'items' => [
                    ['order' => 1, 'nama_item' => 'Salmon Fillet Premium', 'jumlah' => 10, 'satuan' => 'kg', 'harga' => 250000],
                    ['order' => 2, 'nama_item' => 'Nori Sheet', 'jumlah' => 5, 'satuan' => 'pack', 'harga' => 50000],
                    ['order' => 3, 'nama_item' => 'Japanese Rice', 'jumlah' => 20, 'satuan' => 'kg', 'harga' => 15000],
                ]
            ],
            [
                'tanggal' => now()->subDays(3),
                'perihal' => 'Pengadaan Bahan Minuman',
                'alasan'  => 'Restock bahan minuman outlet',
                'outlet_id' => $outlet->id,
                'total' => 0,
                'status' => 'pending',
                'created_by' => $staff->id,
                'approved_by' => null,
                'approved_at' => null,
                'items' => [
                    ['order' => 1, 'nama_item' => 'Syrup Lychee', 'jumlah' => 10, 'satuan' => 'bottle', 'harga' => 30000],
                    ['order' => 2, 'nama_item' => 'Matcha Powder', 'jumlah' => 2, 'satuan' => 'kg', 'harga' => 200000],
                ]
            ],
            [
                'tanggal' => now()->subDays(1),
                'perihal' => 'Penggantian Peralatan Dapur',
                'alasan'  => 'Peralatan rusak & perlu diganti',
                'outlet_id' => $outlet->id,
                'total' => 0,
                'status' => 'rejected',
                'created_by' => $staff->id,
                'approved_by' => $manager->id,
                'approved_at' => now(),
                'rejection_note' => 'Budget tidak mencukupi saat ini.',
                'items' => [
                    ['order' => 1, 'nama_item' => 'Pisau Sushi', 'jumlah' => 2, 'satuan' => 'pcs', 'harga' => 300000],
                    ['order' => 2, 'nama_item' => 'Cutting Board', 'jumlah' => 3, 'satuan' => 'pcs', 'harga' => 100000],
                ]
            ],
        ];

        foreach ($dataPR as $data) {

            // Buat Purchase Requisition
            $pr = PurchaseRequisition::create([
                'tanggal' => $data['tanggal'],
                'perihal' => $data['perihal'],
                'alasan' => $data['alasan'],
                'outlet_id' => $data['outlet_id'],
                'status' => $data['status'],
                'created_by' => $data['created_by'],
                'approved_by' => $data['approved_by'],
                'approved_at' => $data['approved_at'],
                'rejection_note' => $data['rejection_note'] ?? null,
                'total' => 0,
            ]);

            $total = 0;

            // Buat item PR
            foreach ($data['items'] as $item) {
                $subtotal = $item['jumlah'] * $item['harga'];
                $total += $subtotal;

                PrItem::create([
                    'purchase_requisition_id' => $pr->id,
                    'order' => $item['order'],
                    'nama_item' => $item['nama_item'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'harga' => $item['harga'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total PR
            $pr->update(['total' => $total]);
        }

        $this->command->info("Purchase Requisitions & Items seeded successfully!");
    }
}