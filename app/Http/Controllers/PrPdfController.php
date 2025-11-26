<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use App\Services\PdfGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrPdfController extends Controller
{
    protected $pdfService;

    public function __construct(PdfGeneratorService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Download PDF
     */
    public function download($id)
    {
        $pr = PurchaseRequisition::with(['items', 'outlet', 'creator', 'approver'])
            ->findOrFail($id);

        // Authorization check
        $this->authorize($pr);

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('PR PDF downloaded');

        return $this->pdfService->downloadPdf($pr);
    }

    /**
     * Preview PDF in browser
     */
    public function preview($id)
    {
        $pr = PurchaseRequisition::with(['items', 'outlet', 'creator', 'approver'])
            ->findOrFail($id);

        // Authorization check
        $this->authorize($pr);

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($pr)
            ->log('PR PDF previewed');

        return $this->pdfService->streamPdf($pr);
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