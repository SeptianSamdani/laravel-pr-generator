<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use App\Services\PrDocxGeneratorService;
use Illuminate\Support\Facades\Auth;

class PrDocxController extends Controller
{
    protected $docxService;

    public function __construct(PrDocxGeneratorService $docxService)
    {
        $this->docxService = $docxService;
    }

    /**
     * Download DOCX
     */
    public function downloadDocx($id)
    {
        $pr = PurchaseRequisition::with([
            'items', 
            'outlet', 
            'creator', 
            'approver',
            'invoices'
        ])->findOrFail($id);

        // Authorization check
        $this->authorize($pr);

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('PR DOCX downloaded');

        return $this->docxService->downloadDocx($pr);
    }

    /**
     * Download PDF (converted from DOCX)
     */
    public function downloadPdf($id)
    {
        $pr = PurchaseRequisition::with([
            'items', 
            'outlet', 
            'creator', 
            'approver',
            'invoices'
        ])->findOrFail($id);

        // Authorization check
        $this->authorize($pr);

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('PR PDF downloaded (from DOCX template)');

        return $this->docxService->downloadPdf($pr);
    }

    /**
     * Preview PDF in browser
     */
    public function previewPdf($id)
    {
        $pr = PurchaseRequisition::with([
            'items', 
            'outlet', 
            'creator', 
            'approver',
            'invoices'
        ])->findOrFail($id);

        // Authorization check
        $this->authorize($pr);

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('PR PDF previewed');

        return $this->docxService->streamPdf($pr);
    }

    /**
     * Check if user can access this PR
     */
    private function authorize($pr)
    {
        $user = Auth::user();

        // Super admin, admin, and manager can access all PRs
        if ($user->hasAnyRole(['super_admin', 'admin', 'manager'])) {
            return true;
        }

        // Staff can only access their own PRs
        if ($pr->created_by !== $user->id) {
            abort(403, 'Unauthorized access to this PR');
        }

        return true;
    }
}