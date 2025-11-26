<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequisition;
use App\Models\PrItem;
use App\Models\PrInvoice;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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

        // Ensure storage directories exist
        Storage::makeDirectory('public/invoices');
        Storage::makeDirectory('public/signatures');
        Storage::makeDirectory('public/payment-proofs');

        $dataPR = [
            // PR #1 - Approved dengan signature & payment proof
            [
                'tanggal' => now()->subDays(10),
                'perihal' => 'Campaign Instagram - Food Influencer',
                'alasan'  => 'Untuk campaign promosi menu baru',
                'outlet_id' => $outlet->id,
                'status' => 'paid',
                'created_by' => $staff->id,
                'approved_by' => $manager->id,
                'approved_at' => now()->subDays(9),
                'manager_signature_path' => 'signatures/manager-signature-1.png',
                'payment_date' => now()->subDays(8),
                'payment_amount' => 5000000,
                'payment_bank' => 'BCA',
                'payment_account_number' => '1234567890',
                'payment_account_name' => 'John Doe (Talent)',
                'payment_proof_path' => 'payment-proofs/transfer-proof-1.jpg',
                'payment_uploaded_at' => now()->subDays(8),
                'items' => [
                    ['order' => 1, 'nama_item' => 'Instagram Feed Post (3 posts)', 'jumlah' => 3, 'satuan' => 'post', 'harga' => 1000000],
                    ['order' => 2, 'nama_item' => 'Instagram Story (5 stories)', 'jumlah' => 5, 'satuan' => 'story', 'harga' => 400000],
                ],
                'invoices' => [
                    ['file_name' => 'Invoice-Talent-John-001.pdf'],
                ]
            ],

            // PR #2 - Approved dengan signature, belum ada payment proof
            [
                'tanggal' => now()->subDays(5),
                'perihal' => 'Campaign TikTok - Food Review',
                'alasan'  => 'Untuk campaign di TikTok',
                'outlet_id' => $outlet->id,
                'status' => 'approved',
                'created_by' => $staff->id,
                'approved_by' => $manager->id,
                'approved_at' => now()->subDays(4),
                'manager_signature_path' => 'signatures/manager-signature-2.png',
                'payment_date' => null,
                'payment_amount' => null,
                'payment_bank' => null,
                'payment_account_number' => null,
                'payment_account_name' => null,
                'payment_proof_path' => null,
                'payment_uploaded_at' => null,
                'items' => [
                    ['order' => 1, 'nama_item' => 'TikTok Video Review', 'jumlah' => 2, 'satuan' => 'video', 'harga' => 1500000],
                ],
                'invoices' => [
                    ['file_name' => 'Invoice-Talent-Jane-002.pdf'],
                ]
            ],

            // PR #3 - Submitted (pending approval)
            [
                'tanggal' => now()->subDays(2),
                'perihal' => 'Campaign YouTube - Mukbang',
                'alasan'  => 'Untuk campaign di YouTube channel',
                'outlet_id' => $outlet->id,
                'status' => 'submitted',
                'created_by' => $staff->id,
                'approved_by' => null,
                'approved_at' => null,
                'manager_signature_path' => null,
                'payment_date' => null,
                'payment_amount' => null,
                'payment_bank' => null,
                'payment_account_number' => null,
                'payment_account_name' => null,
                'payment_proof_path' => null,
                'payment_uploaded_at' => null,
                'items' => [
                    ['order' => 1, 'nama_item' => 'YouTube Mukbang Video', 'jumlah' => 1, 'satuan' => 'video', 'harga' => 3000000],
                    ['order' => 2, 'nama_item' => 'YouTube Short', 'jumlah' => 3, 'satuan' => 'short', 'harga' => 500000],
                ],
                'invoices' => [
                    ['file_name' => 'Invoice-Talent-Mike-003.pdf'],
                    ['file_name' => 'Invoice-Talent-Mike-003-details.jpg'],
                ]
            ],

            // PR #4 - Draft
            [
                'tanggal' => now()->subDays(1),
                'perihal' => 'Campaign Twitter/X - Thread Promosi',
                'alasan'  => 'Campaign untuk X (Twitter)',
                'outlet_id' => $outlet->id,
                'status' => 'draft',
                'created_by' => $staff->id,
                'approved_by' => null,
                'approved_at' => null,
                'manager_signature_path' => null,
                'payment_date' => null,
                'payment_amount' => null,
                'payment_bank' => null,
                'payment_account_number' => null,
                'payment_account_name' => null,
                'payment_proof_path' => null,
                'payment_uploaded_at' => null,
                'items' => [
                    ['order' => 1, 'nama_item' => 'Thread Promosi', 'jumlah' => 5, 'satuan' => 'thread', 'harga' => 200000],
                ],
                'invoices' => [
                    ['file_name' => 'Invoice-Talent-Sarah-004.pdf'],
                ]
            ],

            // PR #5 - Rejected
            [
                'tanggal' => now()->subDays(7),
                'perihal' => 'Campaign Facebook - Boosted Post',
                'alasan'  => 'Untuk boost post di Facebook',
                'outlet_id' => $outlet->id,
                'status' => 'rejected',
                'created_by' => $staff->id,
                'approved_by' => $manager->id,
                'approved_at' => now()->subDays(6),
                'rejection_note' => 'Budget campaign bulan ini sudah habis. Mohon submit ulang bulan depan.',
                'manager_signature_path' => null,
                'payment_date' => null,
                'payment_amount' => null,
                'payment_bank' => null,
                'payment_account_number' => null,
                'payment_account_name' => null,
                'payment_proof_path' => null,
                'payment_uploaded_at' => null,
                'items' => [
                    ['order' => 1, 'nama_item' => 'Facebook Boosted Post', 'jumlah' => 10, 'satuan' => 'post', 'harga' => 150000],
                ],
                'invoices' => [
                    ['file_name' => 'Invoice-Talent-David-005.pdf'],
                ]
            ],
        ];

        foreach ($dataPR as $data) {
            // Create Purchase Requisition
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
                'manager_signature_path' => $data['manager_signature_path'],
                'payment_date' => $data['payment_date'],
                'payment_amount' => $data['payment_amount'],
                'payment_bank' => $data['payment_bank'],
                'payment_account_number' => $data['payment_account_number'],
                'payment_account_name' => $data['payment_account_name'],
                'payment_proof_path' => $data['payment_proof_path'],
                'payment_uploaded_at' => $data['payment_uploaded_at'],
                'total' => 0,
            ]);

            $total = 0;

            // Create PR Items
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

            // Create Invoice Records (dummy data)
            if (isset($data['invoices'])) {
                foreach ($data['invoices'] as $invoice) {
                    PrInvoice::create([
                        'purchase_requisition_id' => $pr->id,
                        'file_name' => $invoice['file_name'],
                        'file_path' => 'invoices/' . $invoice['file_name'], // Dummy path
                        'file_type' => str_ends_with($invoice['file_name'], '.pdf') ? 'application/pdf' : 'image/jpeg',
                        'file_size' => rand(100000, 500000), // Random size 100KB - 500KB
                        'uploaded_by' => $staff->id,
                    ]);
                }
            }
        }

        $this->command->info("✅ Purchase Requisitions, Items & Invoices seeded successfully!");
        $this->command->info("📊 Total PRs created: " . PurchaseRequisition::count());
        $this->command->info("📄 Total Invoices: " . PrInvoice::count());
    }
}