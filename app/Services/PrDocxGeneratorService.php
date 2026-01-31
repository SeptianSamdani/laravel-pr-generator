<?php

namespace App\Services;

use App\Models\PurchaseRequisition;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Log;

class PrDocxGeneratorService
{
    protected $templatePath;
    protected $exportPath;

    public function __construct()
    {
        $this->templatePath = storage_path('templates/pr-template.docx');
        $this->exportPath = storage_path('app/pr-exports');
        
        if (!file_exists($this->exportPath)) {
            mkdir($this->exportPath, 0755, true);
        }
    }

    public function generateDocx(PurchaseRequisition $pr): string
    {
        if (!file_exists($this->templatePath)) {
            throw new \Exception("Template not found. Run: php artisan pr:generate-template");
        }

        $processor = new TemplateProcessor($this->templatePath);

        // 1. BASIC INFO
        $processor->setValue('tanggal', $pr->tanggal->format('d/m/Y'));
        $processor->setValue('perihal', $pr->perihal);
        $processor->setValue('alasan', $pr->alasan ?? '-');
        $processor->setValue('outlet', $pr->outlet->name);

        // 2. STAFF/CREATOR INFO
        $processor->setValue('staff_name', $pr->creator->name);
        $processor->setValue('staff_date', $pr->created_at->format('d/m/Y'));

        // STAFF SIGNATURE
        if ($pr->hasStaffSignature()) {
            $staffSignaturePath = $this->getSignatureFullPath($pr->staff_signature_path);
            if (file_exists($staffSignaturePath)) {
                try {
                    $processor->setImageValue('staff_signature', [
                        'path' => $staffSignaturePath,
                        'width' => 120,
                        'height' => 60,
                        'ratio' => true
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to insert staff signature: ' . $e->getMessage());
                    $processor->setValue('staff_signature', '[Signature]');
                }
            } else {
                $processor->setValue('staff_signature', '[Signature Not Found]');
            }
        } else {
            $processor->setValue('staff_signature', '');
        }

        // 3. RECIPIENT INFO
        $processor->setValue('recipient_name', $pr->recipient_name ?? '-');
        $processor->setValue('recipient_bank', $pr->recipient_bank ?? '-');
        $processor->setValue('recipient_account_number', $pr->recipient_account_number ?? '-');
        $processor->setValue('recipient_phone', $pr->recipient_phone ?? '-');

        // 4. ITEMS - Clone untuk 6 rows
        $maxRows = 6;
        $processor->cloneRow('item_no', $maxRows);

        // Fill items yang ada
        foreach ($pr->items as $index => $item) {
            $n = $index + 1;
            $processor->setValue("item_no#$n", $n);
            $processor->setValue("item_jumlah#$n", $item->jumlah);
            $processor->setValue("item_nama#$n", $item->nama_item);
            $processor->setValue("item_satuan#$n", $item->satuan);
            $processor->setValue("item_harga#$n", number_format($item->harga, 0, ',', '.'));
            $processor->setValue("item_subtotal#$n", number_format($item->subtotal, 0, ',', '.'));
        }

        // Fill empty rows
        for ($i = $pr->items->count() + 1; $i <= $maxRows; $i++) {
            $processor->setValue("item_no#$i", '');
            $processor->setValue("item_jumlah#$i", '');
            $processor->setValue("item_nama#$i", '');
            $processor->setValue("item_satuan#$i", '');
            $processor->setValue("item_harga#$i", '');
            $processor->setValue("item_subtotal#$i", '');
        }

        // 5. TOTAL
        $processor->setValue('total', 'Rp ' . number_format($pr->total, 0, ',', '.'));

        // 6. MANAGER INFO
        if ($pr->isApproved() || $pr->isPaid() || $pr->isRejected()) {
            $processor->setValue('manager_name', $pr->approver->name ?? '-');
            $processor->setValue('manager_date', $pr->approved_at ? $pr->approved_at->format('d/m/Y') : '-');

            // MANAGER SIGNATURE
            if ($pr->hasManagerSignature()) {
                $managerSignaturePath = $this->getSignatureFullPath($pr->manager_signature_path);
                if (file_exists($managerSignaturePath)) {
                    try {
                        $processor->setImageValue('manager_signature', [
                            'path' => $managerSignaturePath,
                            'width' => 120,
                            'height' => 60,
                            'ratio' => true
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to insert manager signature: ' . $e->getMessage());
                        $processor->setValue('manager_signature', '[Signature]');
                    }
                } else {
                    $processor->setValue('manager_signature', '[Signature Not Found]');
                }
            } else {
                $processor->setValue('manager_signature', '');
            }
        } else {
            $processor->setValue('manager_name', '');
            $processor->setValue('manager_date', '');
            $processor->setValue('manager_signature', '');
        }

        // 7. INVOICES
        if ($pr->invoices->count() > 0) {
            $invoiceImages = [];
            
            foreach ($pr->invoices as $invoice) {
                $invoicePath = storage_path('app/public/' . $invoice->file_path);
                
                if (file_exists($invoicePath)) {
                    // Skip PDF files
                    if ($invoice->file_type === 'application/pdf') {
                        continue;
                    }
                    
                    $invoiceImages[] = [
                        'path' => $invoicePath,
                        'width' => 400,
                        'height' => 300,
                        'ratio' => true
                    ];
                }
            }
            
            // Set first invoice image
            if (!empty($invoiceImages)) {
                try {
                    $processor->setImageValue('invoices', [
                        'path' => $invoiceImages[0]['path'],
                        'width' => 600,    
                        'height' => 450,   
                        'ratio' => true
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to insert invoice image: ' . $e->getMessage());
                    $processor->setValue('invoices', '[Invoice Image]');
                }
            } else {
                $processor->setValue('invoices', '');
            }
        } else {
            $processor->setValue('invoices', 'Tidak ada invoice');
        }

        // 8. SAVE
        $outputFileName = "PR-{$pr->pr_number}-" . now()->format('YmdHis') . ".docx";
        $outputPath = $this->exportPath . '/' . $outputFileName;
        
        $processor->saveAs($outputPath);

        Log::info("DOCX generated: $outputPath");

        return $outputPath;
    }

    public function downloadDocx(PurchaseRequisition $pr)
    {
        $filePath = $this->generateDocx($pr);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function generatePdf(PurchaseRequisition $pr): string
    {
        $docxPath = $this->generateDocx($pr);
        $pdfPath = str_replace('.docx', '.pdf', $docxPath);
        
        // Find LibreOffice
        $libreOfficePath = $this->findLibreOffice();
        
        if (!$libreOfficePath) {
            Log::error('LibreOffice not found');
            throw new \Exception('LibreOffice tidak ditemukan. Pastikan LibreOffice sudah terinstall.');
        }
        
        Log::info("Using LibreOffice at: $libreOfficePath");
        
        // Build command based on OS
        if ($this->isWindows()) {
            // Windows command
            $command = sprintf(
                '"%s" --headless --convert-to pdf --outdir "%s" "%s"',
                $libreOfficePath,
                dirname($docxPath),
                $docxPath
            );
        } else {
            // Linux command
            $command = sprintf(
                '%s --headless --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg($libreOfficePath),
                escapeshellarg(dirname($docxPath)),
                escapeshellarg($docxPath)
            );
        }

        Log::info("Executing command: $command");
        
        exec($command, $output, $returnCode);
        
        Log::info("Command output: " . implode("\n", $output));
        Log::info("Return code: $returnCode");

        // Wait a bit for file to be created
        sleep(1);

        if (!file_exists($pdfPath)) {
            $errorMsg = "PDF file not created. Output: " . implode("\n", $output);
            Log::error($errorMsg);
            throw new \Exception("Konversi PDF gagal. Pastikan LibreOffice terinstall dengan benar.");
        }

        return $pdfPath;
    }

    /**
     * Find LibreOffice installation path
     */
    protected function findLibreOffice(): ?string
    {
        if ($this->isWindows()) {
            // Windows paths
            $possiblePaths = [
                'C:\Program Files\LibreOffice\program\soffice.exe',
                'C:\Program Files (x86)\LibreOffice\program\soffice.exe',
                getenv('PROGRAMFILES') . '\LibreOffice\program\soffice.exe',
                getenv('PROGRAMFILES(X86)') . '\LibreOffice\program\soffice.exe',
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    return $path;
                }
            }
        } else {
            // Linux paths
            $possiblePaths = [
                '/usr/bin/soffice',
                '/usr/bin/libreoffice',
                '/snap/bin/libreoffice',
            ];

            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    return $path;
                }
            }
            
            // Try using 'which' command
            exec("which soffice 2>/dev/null", $output, $returnCode);
            if ($returnCode === 0 && !empty($output)) {
                return trim($output[0]);
            }
        }

        return null;
    }

    /**
     * Check if running on Windows
     */
    protected function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public function downloadPdf(PurchaseRequisition $pr)
    {
        $filePath = $this->generatePdf($pr);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function streamPdf(PurchaseRequisition $pr)
    {
        $filePath = $this->generatePdf($pr);
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    protected function getSignatureFullPath(string $path): string
    {
        if (strpos($path, 'app/public') !== false) {
            return storage_path($path);
        }
        
        if (strpos($path, 'public/') === 0) {
            return storage_path('app/' . $path);
        }
        
        return storage_path('app/public/' . $path);
    }

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

        return $deleted;
    }
}