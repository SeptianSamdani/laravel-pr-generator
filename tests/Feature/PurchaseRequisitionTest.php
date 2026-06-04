<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PurchaseRequisition;
use App\Models\PrItem;
use App\Models\PrInvoice;
use App\Models\Outlet;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\PrList;
use App\Livewire\PrForm;
use App\Livewire\PrDetail;

class PurchaseRequisitionTest extends TestCase
{
    // ─── Authorization: List ─────────────────────────────────────────────────

    /** @test */
    public function guest_cannot_access_pr_list(): void
    {
        $this->get(route('pr.index'))->assertRedirect(route('login'));
    }

    /** @test */
    public function staff_can_access_pr_list(): void
    {
        $this->actingAs($this->createStaff())
            ->get(route('pr.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function manager_can_access_pr_list(): void
    {
        $this->actingAs($this->createManager())
            ->get(route('pr.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_pr_list(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route('pr.index'))
            ->assertStatus(200);
    }

    // ─── Authorization: Create ───────────────────────────────────────────────

    /** @test */
    public function staff_can_access_pr_create(): void
    {
        $this->actingAs($this->createStaff())
            ->get(route('pr.create'))
            ->assertStatus(200);
    }

    /** @test */
    public function viewer_cannot_access_pr_create(): void
    {
        $this->actingAs($this->createViewer())
            ->get(route('pr.create'))
            ->assertStatus(403);
    }

    /** @test */
    public function manager_cannot_access_pr_create(): void
    {
        $this->actingAs($this->createManager())
            ->get(route('pr.create'))
            ->assertStatus(403);
    }

    // ─── PR List: Staff sees only own PRs ────────────────────────────────────

    /** @test */
    public function staff_only_sees_their_own_prs_in_list(): void
    {
        $staff1 = $this->createStaff();
        $staff2 = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr1 = PurchaseRequisition::factory()->create(['created_by' => $staff1->id, 'outlet_id' => $outlet->id]);
        $pr2 = PurchaseRequisition::factory()->create(['created_by' => $staff2->id, 'outlet_id' => $outlet->id]);

        Livewire::actingAs($staff1)
            ->test(PrList::class)
            ->assertSee($pr1->pr_number)
            ->assertDontSee($pr2->pr_number);
    }

    /** @test */
    public function manager_sees_all_prs_in_list(): void
    {
        $staff1  = $this->createStaff();
        $staff2  = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();

        $pr1 = PurchaseRequisition::factory()->create(['created_by' => $staff1->id, 'outlet_id' => $outlet->id]);
        $pr2 = PurchaseRequisition::factory()->create(['created_by' => $staff2->id, 'outlet_id' => $outlet->id]);

        Livewire::actingAs($manager)
            ->test(PrList::class)
            ->assertSee($pr1->pr_number)
            ->assertSee($pr2->pr_number);
    }

    // ─── PR Form: Create Draft ────────────────────────────────────────────────

    /** @test */
    public function staff_can_save_pr_as_draft(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        Livewire::actingAs($staff)
            ->test(PrForm::class)
            ->set('tanggal', now()->format('Y-m-d'))
            ->set('perihal', 'Campaign Instagram')
            ->set('alasan', 'Untuk promosi menu baru')
            ->set('outlet_id', $outlet->id)
            ->set('items', [[
                'id'       => null,
                'nama_item' => 'IG Post',
                'jumlah'   => 3,
                'satuan'   => 'post',
                'harga'    => 500000,
                'subtotal' => 1500000,
            ]])
            ->set('total', 1500000)
            ->call('saveDraft')
            ->assertHasNoErrors()
            ->assertRedirect(route('pr.show', PurchaseRequisition::first()->id));

        $this->assertDatabaseHas('purchase_requisitions', [
            'perihal'    => 'Campaign Instagram',
            'status'     => 'draft',
            'created_by' => $staff->id,
        ]);
    }

    /** @test */
    public function pr_form_validates_required_fields(): void
    {
        $staff = $this->createStaff();

        Livewire::actingAs($staff)
            ->test(PrForm::class)
            ->set('tanggal', '')
            ->set('perihal', '')
            ->set('outlet_id', '')
            ->call('saveDraft')
            ->assertHasErrors(['tanggal', 'perihal', 'outlet_id']);
    }

    /** @test */
    public function pr_form_requires_at_least_one_item(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        Livewire::actingAs($staff)
            ->test(PrForm::class)
            ->set('tanggal', now()->format('Y-m-d'))
            ->set('perihal', 'Test PR')
            ->set('outlet_id', $outlet->id)
            ->set('items', [])
            ->call('saveDraft')
            ->assertDispatched('notify');
    }

    /** @test */
    public function pr_calculates_total_correctly(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        Livewire::actingAs($staff)
            ->test(PrForm::class)
            ->set('items', [
                ['id' => null, 'nama_item' => 'Item A', 'jumlah' => 2, 'satuan' => 'pcs', 'harga' => 100000, 'subtotal' => 200000],
                ['id' => null, 'nama_item' => 'Item B', 'jumlah' => 3, 'satuan' => 'pcs', 'harga' => 50000, 'subtotal' => 150000],
            ])
            ->call('calculateTotal')
            ->assertSet('total', 350000);
    }

    /** @test */
    public function staff_can_add_and_remove_items(): void
    {
        $staff = $this->createStaff();

        $component = Livewire::actingAs($staff)->test(PrForm::class);

        // Initially 1 item
        $component->assertCount('items', 1);

        // Add item
        $component->call('addItem')->assertCount('items', 2);

        // Remove item
        $component->call('removeItem', 0)->assertCount('items', 1);
    }

    // ─── PR Form: Submit for Approval ─────────────────────────────────────────

    /** @test */
    public function staff_can_submit_pr_with_complete_data(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        // Create a PR in draft first
        $pr = PurchaseRequisition::factory()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
            'status'     => 'draft',
        ]);

        PrItem::factory()->create(['purchase_requisition_id' => $pr->id]);
        PrInvoice::factory()->create([
            'purchase_requisition_id' => $pr->id,
            'uploaded_by' => $staff->id,
        ]);

        Livewire::actingAs($staff)
            ->test(PrDetail::class, ['id' => $pr->id])
            ->call('submitForApproval');

        $this->assertDatabaseHas('purchase_requisitions', [
            'id'     => $pr->id,
            'status' => 'submitted',
        ]);
    }

    /** @test */
    public function submit_for_approval_requires_recipient_info(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        // Staff tanpa signature & recipient info — submit harus gagal
        $result = Livewire::actingAs($staff)
            ->test(PrForm::class)
            ->set('tanggal', now()->format('Y-m-d'))
            ->set('perihal', 'Test PR')
            ->set('outlet_id', $outlet->id)
            ->set('items', [[
                'id' => null, 'nama_item' => 'Item A',
                'jumlah' => 1, 'satuan' => 'pcs', 'harga' => 100000, 'subtotal' => 100000,
            ]])
            ->set('total', 100000)
            ->set('recipient_name', '')
            ->set('recipient_bank', '')
            ->set('recipient_account_number', '')
            ->call('submitForApproval');

        // PR seharusnya TIDAK berubah status menjadi 'submitted'
        $this->assertDatabaseMissing('purchase_requisitions', ['status' => 'submitted']);
    }

    // ─── PR Detail ────────────────────────────────────────────────────────────

    /** @test */
    public function staff_can_view_their_own_pr_detail(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $this->actingAs($staff)
            ->get(route('pr.show', $pr->id))
            ->assertStatus(200);
    }

    /** @test */
    public function staff_cannot_view_other_staffs_pr(): void
    {
        $staff1 = $this->createStaff();
        $staff2 = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff2->id, 'outlet_id' => $outlet->id]);

        $this->actingAs($staff1)
            ->get(route('pr.show', $pr->id))
            ->assertStatus(403);
    }

    /** @test */
    public function manager_can_view_any_pr_detail(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();
        $pr      = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $this->actingAs($manager)
            ->get(route('pr.show', $pr->id))
            ->assertStatus(200);
    }

    // ─── PR Edit ──────────────────────────────────────────────────────────────

    /** @test */
    public function staff_can_edit_draft_pr(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id, 'status' => 'draft']);

        $this->actingAs($staff)
            ->get(route('pr.edit', $pr->id))
            ->assertStatus(200);
    }

    /** @test */
    public function staff_cannot_edit_submitted_pr(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->submitted()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $this->actingAs($staff)
            ->get(route('pr.edit', $pr->id))
            ->assertStatus(403);
    }

    /** @test */
    public function staff_cannot_edit_other_staffs_pr(): void
    {
        $staff1 = $this->createStaff();
        $staff2 = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff2->id, 'outlet_id' => $outlet->id, 'status' => 'draft']);

        $this->actingAs($staff1)
            ->get(route('pr.edit', $pr->id))
            ->assertStatus(403);
    }

    // ─── PR Delete ────────────────────────────────────────────────────────────

    /** @test */
    public function staff_can_delete_their_own_pr(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id, 'status' => 'draft']);

        Livewire::actingAs($staff)
            ->test(PrList::class)
            ->call('deletePr', $pr->id);

        $this->assertDatabaseMissing('purchase_requisitions', ['id' => $pr->id]);
    }

    /** @test */
    public function staff_cannot_delete_other_staffs_pr(): void
    {
        $staff1 = $this->createStaff();
        $staff2 = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff2->id, 'outlet_id' => $outlet->id]);

        Livewire::actingAs($staff1)
            ->test(PrList::class)
            ->call('deletePr', $pr->id)
            ->assertDispatched('notify');

        $this->assertDatabaseHas('purchase_requisitions', ['id' => $pr->id]);
    }

    /** @test */
    public function manager_cannot_delete_paid_pr(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();
        $pr      = PurchaseRequisition::factory()->paid()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        Livewire::actingAs($manager)
            ->test(PrList::class)
            ->call('deletePr', $pr->id)
            ->assertDispatched('notify');

        $this->assertDatabaseHas('purchase_requisitions', ['id' => $pr->id]);
    }

    /** @test */
    public function manager_can_delete_draft_pr(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();
        $pr      = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id, 'status' => 'draft']);

        Livewire::actingAs($manager)
            ->test(PrList::class)
            ->call('deletePr', $pr->id);

        $this->assertDatabaseMissing('purchase_requisitions', ['id' => $pr->id]);
    }

    /** @test */
    public function manager_can_delete_submitted_pr(): void
    {
        $staff   = $this->createStaff();
        $manager = $this->createManager();
        $outlet  = $this->createOutlet();
        $pr      = PurchaseRequisition::factory()->submitted()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        Livewire::actingAs($manager)
            ->test(PrList::class)
            ->call('deletePr', $pr->id);

        $this->assertDatabaseMissing('purchase_requisitions', ['id' => $pr->id]);
    }

    // ─── PR Filter & Search ───────────────────────────────────────────────────

    /** @test */
    public function pr_list_can_filter_by_status(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        $draft     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id, 'status' => 'draft']);
        $submitted = PurchaseRequisition::factory()->submitted()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        Livewire::actingAs($staff)
            ->test(PrList::class)
            ->set('statusFilter', 'draft')
            ->assertSee($draft->pr_number)
            ->assertDontSee($submitted->pr_number);
    }

    /** @test */
    public function pr_list_can_search_by_pr_number(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
            'perihal'    => 'Campaign TikTok',
        ]);

        Livewire::actingAs($staff)
            ->test(PrList::class)
            ->set('search', $pr->pr_number)
            ->assertSee($pr->pr_number);
    }

    /** @test */
    public function pr_list_can_reset_filters(): void
    {
        $staff = $this->createStaff();

        Livewire::actingAs($staff)
            ->test(PrList::class)
            ->set('search', 'something')
            ->set('statusFilter', 'draft')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('statusFilter', '');
    }

    // ─── PR Number Auto-generation ────────────────────────────────────────────

    /** @test */
    public function pr_number_is_auto_generated_on_create(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $this->assertNotNull($pr->pr_number);
        $this->assertStringStartsWith('PR-', $pr->pr_number);
    }

    /** @test */
    public function each_pr_gets_unique_pr_number(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr1 = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);
        $pr2 = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $this->assertNotEquals($pr1->pr_number, $pr2->pr_number);
    }

    // ─── PR Permissions ───────────────────────────────────────────────────────

    /** @test */
    public function viewer_can_view_pr_list(): void
    {
        $this->actingAs($this->createViewer())
            ->get(route('pr.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function viewer_cannot_create_pr(): void
    {
        $this->actingAs($this->createViewer())
            ->get(route('pr.create'))
            ->assertStatus(403);
    }
}