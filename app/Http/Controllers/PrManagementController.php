<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use App\Models\PrInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PrManagementController extends Controller
{
    /**
     * Upload invoice files (Staff only)
     */
    public function uploadInvoice(Request $request, $prId)
    {
        $pr = PurchaseRequisition::findOrFail($prId);

        try {
            // Authorization
            if ($pr->created_by !== Auth::id() && !Auth::user()->can('pr.edit')) {
                abort(403, 'Unauthorized');
            }

            // Validate first
            $request->validate([
                'invoices' => 'required|array|max:5',
                'invoices.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]);

            // Total size check
            $totalSize = collect($request->file('invoices'))->sum(fn($f) => $f->getSize());
            if ($totalSize > 26214400) {
                return response()->json(['error' => 'Total file size melebihi 25MB'], 400);
            }

            // Disk space
            $freeSpace = disk_free_space(storage_path('app/public'));
            if ($freeSpace < 104857600) {
                return response()->json(['error' => 'Storage penuh, hubungi admin'], 500);
            }

            $uploaded = [];

            foreach ($request->file('invoices') as $file) {
                $path = $file->store('invoices', 'public');

                $invoice = PrInvoice::create([
                    'purchase_requisition_id' => $pr->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);

                $uploaded[] = $invoice;

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($pr)
                    ->log("Invoice uploaded: {$invoice->file_name}");
            }

            return response()->json([
                'message' => count($uploaded) . ' invoice(s) uploaded successfully',
                'invoices' => $uploaded,
            ]);

        } catch (\Exception $e) {
            \Log::error('Invoice upload failed: '.$e->getMessage());
            return response()->json(['error' => 'Upload gagal, coba lagi'], 500);
        }
    }

    /**
     * Delete invoice (Staff only)
     */
    public function deleteInvoice($invoiceId)
    {
        $invoice = PrInvoice::findOrFail($invoiceId);
        $pr = $invoice->purchaseRequisition;

        // Authorization
        if ($pr->created_by !== Auth::id() && !Auth::user()->can('pr.edit')) {
            abort(403, 'Unauthorized');
        }

        // Can only delete if PR is draft
        if (!$pr->isDraft()) {
            return response()->json([
                'error' => 'Cannot delete invoice after PR is submitted'
            ], 400);
        }

        $fileName = $invoice->file_name;
        $invoice->delete();

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('Invoice deleted: ' . $fileName);

        return response()->json([
            'message' => 'Invoice deleted successfully'
        ]);
    }

    /**
     * Approve PR & Upload Signature (Manager only)
     */
    public function approveWithSignature(Request $request, $prId)
    {
        $pr = PurchaseRequisition::findOrFail($prId);

        // Authorization
        if (!Auth::user()->can('pr.approve')) {
            abort(403, 'Unauthorized');
        }

        // Validate
        $request->validate([
            'signature' => 'required|file|mimes:jpg,jpeg,png|max:2048', // 2MB max
        ]);

        DB::transaction(function () use ($request, $pr) {
            // Store signature
            $signaturePath = $request->file('signature')->store('public/signatures');

            // Update PR
            $pr->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'manager_signature_path' => $signaturePath,
            ]);

            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($pr)
                ->log('PR approved with signature');
        });

        return response()->json([
            'message' => 'PR approved successfully',
            'pr' => $pr->fresh(),
        ]);
    }

    /**
     * Upload Payment Proof (Manager only)
     */
    public function uploadPaymentProof(Request $request, $prId)
    {
        $pr = PurchaseRequisition::findOrFail($prId);

        // Authorization
        if (!Auth::user()->can('pr.approve')) {
            abort(403, 'Unauthorized');
        }

        // Must be approved first
        if (!$pr->isApproved()) {
            return response()->json([
                'error' => 'PR must be approved before uploading payment proof'
            ], 400);
        }

        // Validate
        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0',
            'payment_bank' => 'required|string|max:100',
            'payment_account_number' => 'required|string|max:50',
            'payment_account_name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $pr) {
            // Store payment proof
            $proofPath = $request->file('payment_proof')->store('public/payment-proofs');

            // Update PR
            $pr->update([
                'status' => 'paid',
                'payment_date' => $request->payment_date,
                'payment_amount' => $request->payment_amount,
                'payment_bank' => $request->payment_bank,
                'payment_account_number' => $request->payment_account_number,
                'payment_account_name' => $request->payment_account_name,
                'payment_proof_path' => $proofPath,
                'payment_uploaded_at' => now(),
            ]);

            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($pr)
                ->log('Payment proof uploaded - Amount: Rp ' . number_format($request->payment_amount, 0, ',', '.'));
        });

        return response()->json([
            'message' => 'Payment proof uploaded successfully',
            'pr' => $pr->fresh(),
        ]);
    }

    /**
     * Reject PR (Manager only)
     */
    public function rejectPr(Request $request, $prId)
    {
        $pr = PurchaseRequisition::findOrFail($prId);

        // Authorization
        if (!Auth::user()->can('pr.approve')) {
            abort(403, 'Unauthorized');
        }

        // Validate
        $request->validate([
            'rejection_note' => 'required|string|min:10|max:500',
        ]);

        $pr->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_note' => $request->rejection_note,
        ]);

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('PR rejected: ' . $request->rejection_note);

        return response()->json([
            'message' => 'PR rejected',
            'pr' => $pr->fresh(),
        ]);
    }

    /**
     * Download file (Invoice, Signature, or Payment Proof)
     */
    public function downloadFile($type, $prId)
    {
        $pr = PurchaseRequisition::findOrFail($prId);

        // Authorization check
        if (!Auth::user()->can('pr.view')) {
            abort(403, 'Unauthorized');
        }

        switch ($type) {
            case 'signature':
                if (!$pr->manager_signature_path) {
                    abort(404, 'Signature not found');
                }
                return Storage::download($pr->manager_signature_path);

            case 'payment-proof':
                if (!$pr->payment_proof_path) {
                    abort(404, 'Payment proof not found');
                }
                return Storage::download($pr->payment_proof_path);

            default:
                abort(400, 'Invalid file type');
        }
    }

    /**
     * Download invoice by ID
     */
    public function downloadInvoice($invoiceId)
    {
        $invoice = PrInvoice::findOrFail($invoiceId);

        // Authorization
        if (!Auth::user()->can('pr.view')) {
            abort(403, 'Unauthorized');
        }

        return Storage::download($invoice->file_path, $invoice->file_name);
    }
}