<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PurchaseRequisition;
use App\Models\PrInvoice;
use App\Models\Outlet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use App\Livewire\PrForm;

class InvoiceUploadTest extends TestCase
{
    // ─── Invoice Upload (via Controller) ─────────────────────────────────────

    /** @test */
    public function staff_can_upload_invoices_to_their_pr(): void
    {
        Storage::fake('public');

        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $file = UploadedFile::fake()->image('invoice.jpg', 100, 100);

        $response = $this->actingAs($staff)->post(route('pr.invoice.upload', $pr->id), [
            'invoices' => [$file],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('pr_invoices', [
            'purchase_requisition_id' => $pr->id,
            'uploaded_by'             => $staff->id,
        ]);
    }

    /** @test */
    public function staff_cannot_upload_invoices_to_other_staffs_pr(): void
    {
        Storage::fake('public');

        $staff1 = $this->createStaff();
        $staff2 = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr   = PurchaseRequisition::factory()->create(['created_by' => $staff2->id, 'outlet_id' => $outlet->id]);
        $file = UploadedFile::fake()->image('invoice.jpg');

        $this->actingAs($staff1)
            ->post(route('pr.invoice.upload', $pr->id), ['invoices' => [$file]])
            ->assertStatus(403);
    }

    /** @test */
    public function invoice_upload_validates_file_type(): void
    {
        Storage::fake('public');

        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        // Attempt to upload an invalid file type
        $file = UploadedFile::fake()->create('malware.exe', 100);

        $response = $this->actingAs($staff)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('pr.invoice.upload', $pr->id), [
                'invoices' => [$file],
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function invoice_upload_validates_max_5_files(): void
    {
        Storage::fake('public');

        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $files = [];
        for ($i = 0; $i < 6; $i++) {
            $files[] = UploadedFile::fake()->image("invoice{$i}.jpg");
        }

        $response = $this->actingAs($staff)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('pr.invoice.upload', $pr->id), [
                'invoices' => $files,
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function invoice_upload_requires_at_least_one_file(): void
    {
        Storage::fake('public');

        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $response = $this->actingAs($staff)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('pr.invoice.upload', $pr->id), [
                'invoices' => [],
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function staff_can_upload_pdf_invoice(): void
    {
        Storage::fake('public');

        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $file = UploadedFile::fake()->create('invoice.pdf', 500, 'application/pdf');

        $response = $this->actingAs($staff)->post(route('pr.invoice.upload', $pr->id), [
            'invoices' => [$file],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('pr_invoices', [
            'purchase_requisition_id' => $pr->id,
        ]);
    }

    // ─── Invoice Delete ───────────────────────────────────────────────────────

    /** @test */
    public function staff_can_delete_their_invoice_from_draft_pr(): void
    {
        Storage::fake('public');

        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
            'status'     => 'draft',
        ]);

        Storage::disk('public')->put('invoices/test.jpg', 'content');

        $invoice = PrInvoice::factory()->create([
            'purchase_requisition_id' => $pr->id,
            'uploaded_by'             => $staff->id,
            'file_path'               => 'invoices/test.jpg',
        ]);

        $response = $this->actingAs($staff)->delete(route('pr.invoice.delete', $invoice->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('pr_invoices', ['id' => $invoice->id]);
    }

    /** @test */
    public function staff_cannot_delete_invoice_from_submitted_pr(): void
    {
        Storage::fake('public');

        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->submitted()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
        ]);

        $invoice = PrInvoice::factory()->create([
            'purchase_requisition_id' => $pr->id,
            'uploaded_by'             => $staff->id,
        ]);

        $response = $this->actingAs($staff)->delete(route('pr.invoice.delete', $invoice->id));

        $response->assertStatus(400);
        $this->assertDatabaseHas('pr_invoices', ['id' => $invoice->id]);
    }

    /** @test */
    public function staff_cannot_delete_other_staffs_invoice(): void
    {
        Storage::fake('public');

        $staff1 = $this->createStaff();
        $staff2 = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->create([
            'created_by' => $staff2->id,
            'outlet_id'  => $outlet->id,
            'status'     => 'draft',
        ]);

        $invoice = PrInvoice::factory()->create([
            'purchase_requisition_id' => $pr->id,
            'uploaded_by'             => $staff2->id,
        ]);

        $this->actingAs($staff1)
            ->delete(route('pr.invoice.delete', $invoice->id))
            ->assertStatus(403);
    }

    // ─── Invoice Download ─────────────────────────────────────────────────────

    /** @test */
    public function staff_can_download_their_own_invoice(): void
    {
        Storage::fake('public');

        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        Storage::disk('public')->put('invoices/test.jpg', 'dummy-image-content');

        $invoice = PrInvoice::factory()->create([
            'purchase_requisition_id' => $pr->id,
            'uploaded_by'             => $staff->id,
            'file_path'               => 'invoices/test.jpg',
            'file_name'               => 'test.jpg',
        ]);

        $this->actingAs($staff)
            ->get(route('pr.invoice.download', $invoice->id))
            ->assertStatus(200);
    }

    /** @test */
    public function viewer_cannot_download_invoice(): void
    {
        Storage::fake('public');

        $staff  = $this->createStaff();
        $viewer = $this->createViewer();
        $outlet = $this->createOutlet();

        $pr      = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);
        $invoice = PrInvoice::factory()->create(['purchase_requisition_id' => $pr->id, 'uploaded_by' => $staff->id]);

        $this->actingAs($viewer)
            ->get(route('pr.invoice.download', $invoice->id))
            ->assertStatus(403);
    }

    // ─── Invoice via Livewire Form ────────────────────────────────────────────

    /** @test */
    public function pr_form_can_remove_existing_invoice(): void
    {
        Storage::fake('public');

        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();

        $pr = PurchaseRequisition::factory()->create([
            'created_by' => $staff->id,
            'outlet_id'  => $outlet->id,
            'status'     => 'draft',
        ]);

        Storage::disk('public')->put('invoices/file.jpg', 'content');

        $invoice = PrInvoice::factory()->create([
            'purchase_requisition_id' => $pr->id,
            'uploaded_by'             => $staff->id,
            'file_path'               => 'invoices/file.jpg',
        ]);

        Livewire::actingAs($staff)
            ->test(PrForm::class, ['id' => $pr->id])
            ->call('removeExistingInvoice', $invoice->id);

        $this->assertDatabaseMissing('pr_invoices', ['id' => $invoice->id]);
    }

    // ─── PrInvoice Model Methods ──────────────────────────────────────────────

    /** @test */
    public function invoice_model_correctly_identifies_image_type(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $imgInvoice = PrInvoice::factory()->create([
            'purchase_requisition_id' => $pr->id,
            'uploaded_by'             => $staff->id,
            'file_type'               => 'image/jpeg',
        ]);

        $pdfInvoice = PrInvoice::factory()->create([
            'purchase_requisition_id' => $pr->id,
            'uploaded_by'             => $staff->id,
            'file_type'               => 'application/pdf',
        ]);

        $this->assertTrue($imgInvoice->isImage());
        $this->assertFalse($pdfInvoice->isImage());
        $this->assertTrue($pdfInvoice->isPdf());
        $this->assertFalse($imgInvoice->isPdf());
    }

    /** @test */
    public function invoice_model_formats_file_size_correctly(): void
    {
        $staff  = $this->createStaff();
        $outlet = $this->createOutlet();
        $pr     = PurchaseRequisition::factory()->create(['created_by' => $staff->id, 'outlet_id' => $outlet->id]);

        $invoice = PrInvoice::factory()->create([
            'purchase_requisition_id' => $pr->id,
            'uploaded_by'             => $staff->id,
            'file_size'               => 1048576, // 1 MB
        ]);

        $this->assertStringContainsString('MB', $invoice->getFileSizeFormatted());
    }
}