<?php

namespace App\Services;

use App\Models\PurchaseRequisition;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfGeneratorService
{
    /**
     * Generate PDF for Purchase Requisition
     */
    public function generatePrPdf(PurchaseRequisition $pr)
    {
        $pr->load(['items', 'outlet', 'creator', 'approver']);

        $pdf = Pdf::loadView('pdf.pr-template', [
            'pr' => $pr,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        return $pdf;
    }

    /**
     * Download PDF
     */
    public function downloadPdf(PurchaseRequisition $pr)
    {
        $pdf = $this->generatePrPdf($pr);
        return $pdf->download('PR-' . $pr->pr_number . '.pdf');
    }

    /**
     * Stream PDF (for preview)
     */
    public function streamPdf(PurchaseRequisition $pr)
    {
        $pdf = $this->generatePrPdf($pr);
        return $pdf->stream('PR-' . $pr->pr_number . '.pdf');
    }

    /**
     * Get PDF output as string
     */
    public function getPdfOutput(PurchaseRequisition $pr)
    {
        $pdf = $this->generatePrPdf($pr);
        return $pdf->output();
    }
}