<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PurchaseRequisition;
use App\Models\PrItem;
use App\Models\PrInvoice;
use App\Models\User;
use App\Models\Outlet;
use App\Notifications\PrStatusChanged;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use App\Livewire\PrApproval;
use App\Livewire\PrDetail;

class PrApprovalTest extends TestCase
{
    // ─── Authorization: Approval Page ────────────────────────────────────────

    /** @test */
    public function manager_can_access_approval_page(): void
    {
        $this->actingAs($this->createManager())
            ->get(route('approval.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function staff_cannot_access_approval_page(): void
    {
        $this->actingAs($this->createStaff())
            ->get(route('approval.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function viewer_cannot_access_approval_page(): void
    {
        $this->actingAs($this->createViewer())
            ->get(route('approval.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_approval_page(): void
    {
        $this->get(route('approval.index'))->assertRedirect(route('login'));
    }

    // ─── Approve PR ───────────────────────────────────────────────────────────

    /** @test */
    public function manager_can_approve_submitted_pr(): void
    {
        Storage::fake('public');
        Notification::fake();

        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
        ]);

        // Pre-set manager's signature path
        $signaturePath = 'signatures/manager-sig.png';
        Storage::disk('public')->put($signaturePath, 'fake-image-content');
        $manager->update(['signature_path' => $signaturePath]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->call('openApproveModal', $pr->id)
            ->call('approvePr');

        $this->assertDatabaseHas('purchase_requisitions', [
            'id'          => $pr->id,
            'status'      => 'approved',
            'approved_by' => $manager->id,
        ]);
    }

    /** @test */
    public function approval_requires_manager_signature(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
        ]);

        // Manager has no signature
        $manager->update(['signature_path' => null]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->call('openApproveModal', $pr->id)
            ->call('approvePr')
            ->assertHasErrors(['managerSignature']);

        $this->assertDatabaseHas('purchase_requisitions', [
            'id'     => $pr->id,
            'status' => 'submitted', // unchanged
        ]);
    }

    /** @test */
    public function manager_cannot_approve_their_own_pr(): void
    {
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $manager->id,
            'outlet_id'  => $outlet->id,
        ]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->call('openApproveModal', $pr->id)
            ->assertSet('showApproveModal', false); // modal tidak terbuka karena own PR
    }

    /** @test */
    public function manager_cannot_approve_draft_pr(): void
    {
        Storage::fake('public');

        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $signaturePath = 'signatures/manager.png';
        Storage::disk('public')->put($signaturePath, 'content');
        $manager->update(['signature_path' => $signaturePath]);

        $pr = PurchaseRequisition::factory()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
            'status'     => 'draft',
        ]);

        Livewire::actingAs($manager)
            ->test(PrDetail::class, ['id' => $pr->id])
            ->call('openApproveModal')
            ->assertSet('showApproveModal', false); // draft PR tidak bisa diapprove
    }

    /** @test */
    public function staff_cannot_approve_pr(): void
    {
        $staff1 = $this->createStaff();
        $staff2 = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff2->id,
            'outlet_id'  => $outlet->id,
        ]);

        Livewire::actingAs($staff1)
            ->test(PrDetail::class, ['id' => $pr->id])
            ->assertSet('canApprove', false);
    }

    /** @test */
    public function notification_is_sent_to_creator_on_approval(): void
    {
        Storage::fake('public');
        Notification::fake();

        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
        ]);

        $signaturePath = 'signatures/manager.png';
        Storage::disk('public')->put($signaturePath, 'content');
        $manager->update(['signature_path' => $signaturePath]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->call('openApproveModal', $pr->id)
            ->call('approvePr');

        Notification::assertSentTo($staff, PrStatusChanged::class);
    }

    // ─── Reject PR ────────────────────────────────────────────────────────────

    /** @test */
    public function manager_can_reject_submitted_pr_with_reason(): void
    {
        Notification::fake();

        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
        ]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->call('openRejectModal', $pr->id)
            ->set('rejectionNote', 'Budget bulan ini sudah habis, harap submit ulang bulan depan.')
            ->call('rejectPr');

        $this->assertDatabaseHas('purchase_requisitions', [
            'id'             => $pr->id,
            'status'         => 'rejected',
            'approved_by'    => $manager->id,
        ]);
    }

    /** @test */
    public function rejection_requires_a_reason(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
        ]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->call('openRejectModal', $pr->id)
            ->set('rejectionNote', '')
            ->call('rejectPr')
            ->assertHasErrors(['rejectionNote']);
    }

    /** @test */
    public function rejection_note_must_be_at_least_10_characters(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
        ]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->call('openRejectModal', $pr->id)
            ->set('rejectionNote', 'Short')
            ->call('rejectPr')
            ->assertHasErrors(['rejectionNote']);
    }

    /** @test */
    public function manager_cannot_reject_their_own_pr(): void
    {
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $manager->id,
            'outlet_id'  => $outlet->id,
        ]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->call('openRejectModal', $pr->id)
            ->assertSet('showRejectModal', false); // modal tidak terbuka karena own PR
    }

    /** @test */
    public function notification_is_sent_to_creator_on_rejection(): void
    {
        Notification::fake();

        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
        ]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->call('openRejectModal', $pr->id)
            ->set('rejectionNote', 'Budget bulan ini sudah habis, harap submit ulang.')
            ->call('rejectPr');

        Notification::assertSentTo($staff, PrStatusChanged::class);
    }

    // ─── Bulk Approve ─────────────────────────────────────────────────────────

    /** @test */
    public function manager_can_bulk_approve_with_profile_signature(): void
    {
        Storage::fake('public');
        Notification::fake();

        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $signaturePath = 'signatures/manager.png';
        Storage::disk('public')->put($signaturePath, 'content');
        $manager->update(['signature_path' => $signaturePath]);

        $pr1 = PurchaseRequisition::factory()->submitted()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);
        $pr2 = PurchaseRequisition::factory()->submitted()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->set('selectedPrs', [$pr1->id, $pr2->id])
            ->call('bulkApprove');

        $this->assertDatabaseHas('purchase_requisitions', ['id' => $pr1->id, 'status' => 'approved']);
        $this->assertDatabaseHas('purchase_requisitions', ['id' => $pr2->id, 'status' => 'approved']);
    }

    /** @test */
    public function bulk_approve_fails_if_manager_has_no_signature(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $manager->update(['signature_path' => null]);

        $pr = PurchaseRequisition::factory()->submitted()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->set('selectedPrs', [$pr->id])
            ->call('bulkApprove');

        // PR masih submitted karena manager tidak punya signature
        $this->assertDatabaseHas('purchase_requisitions', ['id' => $pr->id, 'status' => 'submitted']);
    }

    /** @test */
    public function bulk_approve_fails_if_no_pr_selected(): void
    {
        $manager = $this->createManager();

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->set('selectedPrs', [])
            ->call('bulkApprove');

        // Tidak ada PR yang di-approve karena selectedPrs kosong
        $this->assertDatabaseMissing('purchase_requisitions', ['status' => 'approved']);
    }

    // ─── Approval Stats ───────────────────────────────────────────────────────

    /** @test */
    public function approval_page_shows_correct_pending_count(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        PurchaseRequisition::factory()->submitted()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);
        PurchaseRequisition::factory()->submitted()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->assertSee('2');
    }

    // ─── Payment Proof Upload ─────────────────────────────────────────────────

    /** @test */
    public function manager_can_open_payment_modal_for_approved_pr(): void
    {
        Storage::fake('public');

        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->approved()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
            'approved_by' => $manager->id,
        ]);

        Livewire::actingAs($manager)
            ->test(PrDetail::class, ['id' => $pr->id])
            ->call('openPaymentModal')
            ->assertSet('showPaymentModal', true);
    }

    /** @test */
    public function payment_modal_cannot_open_for_non_approved_pr(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
        ]);

        Livewire::actingAs($manager)
            ->test(PrDetail::class, ['id' => $pr->id])
            ->call('openPaymentModal')
            ->assertSet('showPaymentModal', false); // modal tidak terbuka karena PR belum approved
    }

    /** @test */
    public function manager_can_upload_payment_proof_for_approved_pr(): void
    {
        Storage::fake('public');

        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->create([
            'created_by'  => $staff->id,
            'outlet_id'   => $outlet->id,
            'status'      => 'approved',
            'approved_by' => $manager->id,
            'approved_at' => now(),
        ]);

        $fakeFile = UploadedFile::fake()->image('bukti_transfer.jpg', 100, 100);

        Livewire::actingAs($manager)
            ->test(PrDetail::class, ['id' => $pr->id])
            ->set('paymentProof', $fakeFile)
            ->set('paymentDate', now()->format('Y-m-d'))
            ->set('paymentAmount', 5000000)
            ->set('paymentBank', 'BCA')
            ->set('paymentAccountNumber', '1234567890')
            ->set('paymentAccountName', 'John Doe')
            ->call('uploadPaymentProof');

        $this->assertDatabaseHas('purchase_requisitions', [
            'id'     => $pr->id,
            'status' => 'paid',
        ]);
    }

    /** @test */
    public function payment_upload_validates_required_fields(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->create([
            'created_by'  => $staff->id,
            'outlet_id'   => $outlet->id,
            'status'      => 'approved',
            'approved_by' => $manager->id,
            'approved_at' => now(),
        ]);

        Livewire::actingAs($manager)
            ->test(PrDetail::class, ['id' => $pr->id])
            ->set('paymentDate', '')
            ->set('paymentAmount', '')
            ->set('paymentBank', '')
            ->set('paymentAccountNumber', '')
            ->set('paymentAccountName', '')
            ->call('uploadPaymentProof')
            ->assertHasErrors([
                'paymentProof',
                'paymentDate',
                'paymentAmount',
                'paymentBank',
                'paymentAccountNumber',
                'paymentAccountName',
            ]);
    }

    // ─── Approval Filters ─────────────────────────────────────────────────────

    /** @test */
    public function approval_list_can_be_filtered_by_outlet(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet1 = $this->createOutlet();
        $outlet2 = $this->createOutlet();

        $pr1 = PurchaseRequisition::factory()->submitted()->create(['created_by' => $staff->id, 'outlet_id' => $outlet1->id]);
        $pr2 = PurchaseRequisition::factory()->submitted()->create(['created_by' => $staff->id, 'outlet_id' => $outlet2->id]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->set('outletFilter', $outlet1->id)
            ->assertSee($pr1->pr_number)
            ->assertDontSee($pr2->pr_number);
    }

    /** @test */
    public function approval_list_can_be_searched(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
            'perihal'    => 'Campaign YouTube Spesial',
        ]);

        Livewire::actingAs($manager)
            ->test(PrApproval::class)
            ->set('search', 'YouTube Spesial')
            ->assertSee($pr->pr_number);
    }
}