<?php

namespace App\Services;

use App\Models\PurchaseRequisition;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PrDocxGeneratorService
{
    protected $templatePath;
    protected $exportPath;

    public function __construct()
    {
        $this->templatePath = storage_path('templates/pr-template.docx');
        $this->exportPath = storage_path('app/pr-exports');
        
        // Create directories if not exists
        $templateDir = storage_path('templates');
        if (!file_exists($templateDir)) {
            mkdir($templateDir, 0755, true);
        }
        
        if (!file_exists($this->exportPath)) {
            mkdir($this->exportPath, 0755, true);
        }
    }

    /**
     * Generate DOCX dari template
     */
    public function generateDocx(PurchaseRequisition $pr): string
    {
        // Validate template exists
        if (!file_exists($this->templatePath)) {
            throw new \Exception(
                "Template file not found at: {$this->templatePath}\n" .
                "Please run: php artisan pr:generate-template\n" .
                "Or manually create the template file."
            );
        }

        // Validate template is readable
        if (!is_readable($this->templatePath)) {
            throw new \Exception("Template file is not readable: {$this->templatePath}");
        }

        // Validate template is valid ZIP/DOCX
        $zip = new \ZipArchive();
        if ($zip->open($this->templatePath) !== true) {
            throw new \Exception(
                "Template file is corrupted or not a valid DOCX file: {$this->templatePath}\n" .
                "Please regenerate using: php artisan pr:generate-template"
            );
        }
        $zip->close();

        try {
            // Load template
            $processor = new TemplateProcessor($this->templatePath);
        } catch (\Exception $e) {
            throw new \Exception(
                "Failed to load template: {$e->getMessage()}\n" .
                "Template path: {$this->templatePath}"
            );
        }

        // 1. BASIC INFORMATION
        $processor->setValue('pr_number', $pr->pr_number);
        $processor->setValue('tanggal', $pr->tanggal->format('d/m/Y'));
        $processor->setValue('perihal', $pr->perihal);
        $processor->setValue('alasan', $pr->alasan ?? '-');
        $processor->setValue('outlet', $pr->outlet->name);
        $processor->setValue('total', 'Rp ' . number_format($pr->total, 0, ',', '.'));

        // 2. STAFF/CREATOR INFO
        $processor->setValue('staff_name', $pr->creator->name);
        $processor->setValue('staff_date', $pr->created_at->format('d/m/Y'));

        // 3. ITEMS TABLE (Clone rows + fill empty rows)
        $itemCount = $pr->items->count();
        
        if ($itemCount > 0) {
            // Clone the first row for actual items
            $processor->cloneRow('item_no', $itemCount);
            
            foreach ($pr->items as $index => $item) {
                $n = $index + 1;
                $processor->setValue("item_no#$n", $n);
                $processor->setValue("item_jumlah#$n", $item->jumlah);
                $processor->setValue("item_nama#$n", $item->nama_item);
                $processor->setValue("item_satuan#$n", $item->satuan);
                $processor->setValue("item_harga#$n", number_format($item->harga, 0, ',', '.'));
                $processor->setValue("item_subtotal#$n", number_format($item->subtotal, 0, ',', '.'));
            }
        } else {
            // If no items, clear placeholder
            $processor->setValue('item_no', '-');
            $processor->setValue('item_jumlah', '');
            $processor->setValue('item_nama', 'Tidak ada item');
            $processor->setValue('item_satuan', '');
            $processor->setValue('item_harga', '');
            $processor->setValue('item_subtotal', '');
        }

        // 4. MANAGER APPROVAL INFO
        if ($pr->isApproved() || $pr->isPaid() || $pr->isRejected()) {
            $processor->setValue('manager_name', $pr->approver->name ?? '-');
            $processor->setValue('manager_date', $pr->approved_at ? $pr->approved_at->format('d/m/Y') : '-');

            // 5. MANAGER SIGNATURE (Image)
            if ($pr->hasSignature()) {
                $signaturePath = $this->getSignatureFullPath($pr->manager_signature_path);
                
                if (file_exists($signaturePath)) {
                    try {
                        $processor->setImageValue('signature', [
                            'path' => $signaturePath,
                            'width' => 120,
                            'height' => 60,
                            'ratio' => true
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to insert signature image: ' . $e->getMessage());
                        $processor->setValue('signature', '[Signature]');
                    }
                } else {
                    $processor->setValue('signature', '[Signature Not Found]');
                }
            } else {
                $processor->setValue('signature', '');
            }
        } else {
            // PR belum diapprove
            $processor->setValue('manager_name', '');
            $processor->setValue('manager_date', '');
            $processor->setValue('signature', '');
        }

        // 6. REMOVE PAYMENT INFO SECTION (not in client template)
        // Client template tidak memiliki info pembayaran di body
        // Payment info hanya ada di system untuk tracking

        // 7. REMOVE STATUS BADGE (not in client template)
        // Status hanya untuk internal tracking

        // Save output
        $outputFileName = "PR-{$pr->pr_number}-" . now()->format('YmdHis') . ".docx";
        $outputPath = $this->exportPath . '/' . $outputFileName;
        
        $processor->saveAs($outputPath);

        Log::info("DOCX generated successfully: $outputPath");

        return $outputPath;
    }

    /**
     * Generate PDF from DOCX (via LibreOffice)
     * Requires: apt-get install libreoffice
     */
    public function generatePdf(PurchaseRequisition $pr): string
    {
        // First generate DOCX
        $docxPath = $this->generateDocx($pr);

        // Convert to PDF using LibreOffice headless
        $pdfPath = str_replace('.docx', '.pdf', $docxPath);
        
        $command = sprintf(
            'soffice --headless --convert-to pdf --outdir %s %s 2>&1',
            escapeshellarg(dirname($docxPath)),
            escapeshellarg($docxPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($pdfPath)) {
            Log::error('LibreOffice conversion failed', [
                'command' => $command,
                'output' => $output,
                'return_code' => $returnCode
            ]);
            throw new \Exception('Failed to convert DOCX to PDF. Is LibreOffice installed?');
        }

        Log::info("PDF generated successfully: $pdfPath");

        return $pdfPath;
    }

    /**
     * Download DOCX
     */
    public function downloadDocx(PurchaseRequisition $pr)
    {
        $filePath = $this->generateDocx($pr);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    /**
     * Download PDF
     */
    public function downloadPdf(PurchaseRequisition $pr)
    {
        $filePath = $this->generatePdf($pr);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    /**
     * Stream PDF (preview in browser)
     */
    public function streamPdf(PurchaseRequisition $pr)
    {
        $filePath = $this->generatePdf($pr);
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    /**
     * Helper: Get full path signature
     */
    protected function getSignatureFullPath(string $path): string
    {
        // Handle different storage paths
        if (strpos($path, 'app/public') !== false) {
            return storage_path($path);
        }
        
        if (strpos($path, 'public/') === 0) {
            return storage_path('app/' . $path);
        }
        
        return storage_path('app/public/' . $path);
    }

    /**
     * Cleanup old exports (optional)
     */
    public function cleanupOldExports(int $daysOld = 7): int
    {
        $files = glob($this->exportPath . '/*');
        $deleted = 0;
        $cutoffTime = now()->subDays($daysOld)->timestamp;

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                unlink($file);
                $deleted++;
            }
        }

        Log::info("Cleaned up $deleted old export files");
        return $deleted;
    }
}