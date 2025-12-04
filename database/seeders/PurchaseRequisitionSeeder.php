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
            // PR #1 - Paid (Lengkap semua data)
            [
                'tanggal' => now()->subDays(10),
                'perihal' => 'Campaign Instagram - Food Influencer',
                'alasan'  => 'Untuk campaign promosi menu baru',
                'outlet_id' => $outlet->id,
                'status' => 'paid',
                'created_by' => $staff->id,
                'approved_by' => $manager->id,
                'approved_at' => now()->subDays(9),
                'staff_signature_path' => 'signatures/staff-signature-1.png',
                'manager_signature_path' => 'signatures/manager-signature-1.png',
                'payment_date' => now()->subDays(8),
                'payment_amount' => 5000000,
                'payment_bank' => 'BCA',
                'payment_account_number' => '1234567890',
                'payment_account_name' => 'Finance Dept',
                'payment_proof_path' => 'payment-proofs/transfer-proof-1.jpg',
                'payment_uploaded_at' => now()->subDays(8),
                'recipient_name' => 'John Doe',
                'recipient_bank' => 'BCA',
                'recipient_account_number' => '9876543210',
                'recipient_phone' => '081234567890',
                'items' => [
                    ['order' => 1, 'nama_item' => 'Instagram Feed Post (3 posts)', 'jumlah' => 3, 'satuan' => 'post', 'harga' => 1000000],
                    ['order' => 2, 'nama_item' => 'Instagram Story (5 stories)', 'jumlah' => 5, 'satuan' => 'story', 'harga' => 400000],
                ],
                'invoices' => [
                    ['file_name' => 'Invoice-Talent-John-001.pdf'],
                ]
            ],

            // PR #2 - Approved (sudah ditandatangan, belum bayar)
            [
                'tanggal' => now()->subDays(5),
                'perihal' => 'Campaign TikTok - Food Review',
                'alasan'  => 'Untuk campaign di TikTok',
                'outlet_id' => $outlet->id,
                'status' => 'approved',
                'created_by' => $staff->id,
                'approved_by' => $manager->id,
                'approved_at' => now()->subDays(4),
                'staff_signature_path' => 'signatures/staff-signature-2.png',
                'manager_signature_path' => 'signatures/manager-signature-2.png',
                'payment_date' => null,
                'payment_amount' => null,
                'payment_bank' => null,
                'payment_account_number' => null,
                'payment_account_name' => null,
                'payment_proof_path' => null,
                'payment_uploaded_at' => null,
                'recipient_name' => 'Jane Smith',
                'recipient_bank' => 'Mandiri',
                'recipient_account_number' => '5551234567',
                'recipient_phone' => '082345678901',
                'items' => [
                    ['order' => 1, 'nama_item' => 'TikTok Video Review', 'jumlah' => 2, 'satuan' => 'video', 'harga' => 1500000],
                ],
                'invoices' => [
                    ['file_name' => 'Invoice-Talent-Jane-002.pdf'],
                ]
            ],

            // PR #3 - Submitted (menunggu approval)
            [
                'tanggal' => now()->subDays(2),
                'perihal' => 'Campaign YouTube - Mukbang',
                'alasan'  => 'Untuk campaign di YouTube channel',
                'outlet_id' => $outlet->id,
                'status' => 'submitted',
                'created_by' => $staff->id,
                'approved_by' => null,
                'approved_at' => null,
                'staff_signature_path' => 'signatures/staff-signature-3.png',
                'manager_signature_path' => null,
                'payment_date' => null,
                'payment_amount' => null,
                'payment_bank' => null,
                'payment_account_number' => null,
                'payment_account_name' => null,
                'payment_proof_path' => null,
                'payment_uploaded_at' => null,
                'recipient_name' => 'Mike Johnson',
                'recipient_bank' => 'BNI',
                'recipient_account_number' => '3337654321',
                'recipient_phone' => '083456789012',
                'items' => [
                    ['order' => 1, 'nama_item' => 'YouTube Mukbang Video', 'jumlah' => 1, 'satuan' => 'video', 'harga' => 3000000],
                    ['order' => 2, 'nama_item' => 'YouTube Short', 'jumlah' => 3, 'satuan' => 'short', 'harga' => 500000],
                ],
                'invoices' => [
                    ['file_name' => 'Invoice-Talent-Mike-003.pdf'],
                    ['file_name' => 'Invoice-Talent-Mike-003-details.jpg'],
                ]
            ],

            // PR #4 - Draft (belum submit)
            [
                'tanggal' => now()->subDays(1),
                'perihal' => 'Campaign Twitter/X - Thread Promosi',
                'alasan'  => 'Campaign untuk X (Twitter)',
                'outlet_id' => $outlet->id,
                'status' => 'draft',
                'created_by' => $staff->id,
                'approved_by' => null,
                'approved_at' => null,
                'staff_signature_path' => null,
                'manager_signature_path' => null,
                'payment_date' => null,
                'payment_amount' => null,
                'payment_bank' => null,
                'payment_account_number' => null,
                'payment_account_name' => null,
                'payment_proof_path' => null,
                'payment_uploaded_at' => null,
                'recipient_name' => 'Sarah Williams',
                'recipient_bank' => 'BRI',
                'recipient_account_number' => '7779876543',
                'recipient_phone' => '084567890123',
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
                'staff_signature_path' => 'signatures/staff-signature-5.png',
                'manager_signature_path' => null,
                'payment_date' => null,
                'payment_amount' => null,
                'payment_bank' => null,
                'payment_account_number' => null,
                'payment_account_name' => null,
                'payment_proof_path' => null,
                'payment_uploaded_at' => null,
                'recipient_name' => 'David Brown',
                'recipient_bank' => 'CIMB Niaga',
                'recipient_account_number' => '4445678901',
                'recipient_phone' => '085678901234',
                'items' => [
                    ['order' => 1, 'nama_item' => 'Facebook Boosted Post', 'jumlah' => 10, 'satuan' => 'post', 'harga' => 150000],
                ],
                'invoices' => [
                    ['file_name' => 'Invoice-Talent-David-005.pdf'],
                ]
            ],

            // PR #6 - Paid (contoh dengan multiple items)
            [
                'tanggal' => now()->subDays(15),
                'perihal' => 'Campaign Multi Platform - Brand Ambassador',
                'alasan'  => 'Campaign brand ambassador untuk multiple platform',
                'outlet_id' => $outlet->id,
                'status' => 'paid',
                'created_by' => $staff->id,
                'approved_by' => $manager->id,
                'approved_at' => now()->subDays(14),
                'staff_signature_path' => 'signatures/staff-signature-6.png',
                'manager_signature_path' => 'signatures/manager-signature-6.png',
                'payment_date' => now()->subDays(13),
                'payment_amount' => 15000000,
                'payment_bank' => 'BCA',
                'payment_account_number' => '9998887777',
                'payment_account_name' => 'Finance Dept',
                'payment_proof_path' => 'payment-proofs/transfer-proof-6.jpg',
                'payment_uploaded_at' => now()->subDays(13),
                'recipient_name' => 'Lisa Anderson',
                'recipient_bank' => 'BCA',
                'recipient_account_number' => '1112223334',
                'recipient_phone' => '086789012345',
                'items' => [
                    ['order' => 1, 'nama_item' => 'Instagram Content (10 posts)', 'jumlah' => 10, 'satuan' => 'post', 'harga' => 800000],
                    ['order' => 2, 'nama_item' => 'TikTok Video (5 videos)', 'jumlah' => 5, 'satuan' => 'video', 'harga' => 1000000],
                    ['order' => 3, 'nama_item' => 'YouTube Video Review', 'jumlah' => 2, 'satuan' => 'video', 'harga' => 2000000],
                    ['order' => 4, 'nama_item' => 'Instagram Story Takeover', 'jumlah' => 1, 'satuan' => 'day', 'harga' => 1000000],
                ],
                'invoices' => [
                    ['file_name' => 'Invoice-Talent-Lisa-006.pdf'],
                    ['file_name' => 'Contract-Lisa-006.pdf'],
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
                'staff_signature_path' => $data['staff_signature_path'],
                'manager_signature_path' => $data['manager_signature_path'],
                'payment_date' => $data['payment_date'],
                'payment_amount' => $data['payment_amount'],
                'payment_bank' => $data['payment_bank'],
                'payment_account_number' => $data['payment_account_number'],
                'payment_account_name' => $data['payment_account_name'],
                'payment_proof_path' => $data['payment_proof_path'],
                'payment_uploaded_at' => $data['payment_uploaded_at'],
                'recipient_name' => $data['recipient_name'],
                'recipient_bank' => $data['recipient_bank'],
                'recipient_account_number' => $data['recipient_account_number'],
                'recipient_phone' => $data['recipient_phone'],
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

            // Create Invoice Records
            if (isset($data['invoices'])) {
                foreach ($data['invoices'] as $invoice) {
                    PrInvoice::create([
                        'purchase_requisition_id' => $pr->id,
                        'file_name' => $invoice['file_name'],
                        'file_path' => 'invoices/' . $invoice['file_name'],
                        'file_type' => str_ends_with($invoice['file_name'], '.pdf') ? 'application/pdf' : 'image/jpeg',
                        'file_size' => rand(100000, 500000),
                        'uploaded_by' => $data['created_by'],
                    ]);
                }
            }
        }

        $this->command->info("✅ Purchase Requisitions seeded successfully!");
        $this->command->info("📊 Total PRs: " . PurchaseRequisition::count());
        $this->command->info("📋 Total Items: " . PrItem::count());
        $this->command->info("📄 Total Invoices: " . PrInvoice::count());
        $this->command->newLine();
        $this->command->info("Status breakdown:");
        $this->command->info("  - Draft: " . PurchaseRequisition::where('status', 'draft')->count());
        $this->command->info("  - Submitted: " . PurchaseRequisition::where('status', 'submitted')->count());
        $this->command->info("  - Approved: " . PurchaseRequisition::where('status', 'approved')->count());
        $this->command->info("  - Paid: " . PurchaseRequisition::where('status', 'paid')->count());
        $this->command->info("  - Rejected: " . PurchaseRequisition::where('status', 'rejected')->count());
    }
}