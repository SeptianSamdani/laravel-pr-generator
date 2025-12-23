<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Font;

class GeneratePrTemplate extends Command
{
    protected $signature = 'pr:generate-template';
    protected $description = 'Generate PR DOCX template file';

    public function handle()
    {
        $templatePath = storage_path('templates/pr-template.docx');
        
        // Create templates directory if not exists
        if (!file_exists(storage_path('templates'))) {
            mkdir(storage_path('templates'), 0755, true);
        }

        try {
            $phpWord = new PhpWord();
            
            // Set default font
            $phpWord->setDefaultFontName('Arial');
            $phpWord->setDefaultFontSize(11);

            // Add section
            $section = $phpWord->addSection([
                'marginTop' => 1134,    // 2cm
                'marginBottom' => 1134,
                'marginLeft' => 1134,
                'marginRight' => 1134,
            ]);

            // ==========================================
            // HEADER WITH LOGO (Placeholder)
            // ==========================================
            $logoPath = public_path('sushi-mentai-logo.png');
            if (file_exists($logoPath)) {
                $section->addImage($logoPath, [
                    'width' => 150,
                    'height' => 50,
                    'alignment' => Jc::CENTER,
                ]);
            } else {
                $section->addText(
                    'SUSHI MENTAI',
                    ['size' => 18, 'bold' => true, 'color' => 'F97316'],
                    ['alignment' => Jc::CENTER]
                );
            }

            // Title
            $section->addText(
                'PURCHASE REQUISITION',
                ['size' => 20, 'bold' => true, 'color' => 'F97316'],
                ['alignment' => Jc::CENTER, 'spaceAfter' => 300]
            );

            // ==========================================
            // ADDRESS SECTION
            // ==========================================
            $section->addText(
                'Kepada: Head Office',
                ['size' => 11, 'bold' => true, 'color' => 'F97316'],
                ['spaceAfter' => 80]
            );
            $section->addText(
                'Ruko Darwin Barat No.3, Gading Serpong Kel. Medang',
                ['size' => 10],
                ['spaceAfter' => 0]
            );
            $section->addText(
                'Kec. Pagedangan Kabupaten Tanggerang, Banten 15334',
                ['size' => 10],
                ['spaceAfter' => 300]
            );

            // ==========================================
            // INFO TABLE (4 columns)
            // ==========================================
            $infoTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => 'DDDDDD',
                'cellMargin' => 80,
            ]);

            // Row 1
            $infoTable->addRow();
            $infoTable->addCell(2500, ['bgColor' => 'FFF5F0'])->addText('Tanggal', ['bold' => true, 'color' => 'F97316', 'size' => 10]);
            $infoTable->addCell(2500)->addText('${tanggal}', ['size' => 10]);
            $infoTable->addCell(2500, ['bgColor' => 'FFF5F0'])->addText('Perihal', ['bold' => true, 'color' => 'F97316', 'size' => 10]);
            $infoTable->addCell(2500)->addText('${perihal}', ['size' => 10]);

            // Row 2
            $infoTable->addRow();
            $infoTable->addCell(2500, ['bgColor' => 'FFF5F0'])->addText('Alasan', ['bold' => true, 'color' => 'F97316', 'size' => 10]);
            $infoTable->addCell(2500)->addText('${alasan}', ['size' => 10]);
            $infoTable->addCell(2500, ['bgColor' => 'FFF5F0'])->addText('Outlet', ['bold' => true, 'color' => 'F97316', 'size' => 10]);
            $infoTable->addCell(2500)->addText('${outlet}', ['size' => 10]);

            $section->addTextBreak(1);

            // ==========================================
            // ITEMS TABLE
            // ==========================================
            $itemsTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => 'DDDDDD',
                'cellMargin' => 80,
            ]);

            // Header Row
            $itemsTable->addRow(400);
            $itemsTable->addCell(800, ['bgColor' => 'F97316', 'valign' => 'center'])->addText('NO', ['bold' => true, 'color' => 'FFFFFF', 'size' => 10], ['alignment' => Jc::CENTER]);
            $itemsTable->addCell(1200, ['bgColor' => 'F97316', 'valign' => 'center'])->addText('JUMLAH', ['bold' => true, 'color' => 'FFFFFF', 'size' => 10], ['alignment' => Jc::CENTER]);
            $itemsTable->addCell(4000, ['bgColor' => 'F97316', 'valign' => 'center'])->addText('NAMA ITEM', ['bold' => true, 'color' => 'FFFFFF', 'size' => 10], ['alignment' => Jc::CENTER]);
            $itemsTable->addCell(1200, ['bgColor' => 'F97316', 'valign' => 'center'])->addText('SATUAN', ['bold' => true, 'color' => 'FFFFFF', 'size' => 10], ['alignment' => Jc::CENTER]);
            $itemsTable->addCell(1400, ['bgColor' => 'F97316', 'valign' => 'center'])->addText('HARGA', ['bold' => true, 'color' => 'FFFFFF', 'size' => 10], ['alignment' => Jc::CENTER]);
            $itemsTable->addCell(1400, ['bgColor' => 'F97316', 'valign' => 'center'])->addText('SUBTOTAL', ['bold' => true, 'color' => 'FFFFFF', 'size' => 10], ['alignment' => Jc::CENTER]);

            // Data Row (Template for cloning)
            $itemsTable->addRow();
            $itemsTable->addCell(800)->addText('${item_no}', ['size' => 10], ['alignment' => Jc::CENTER]);
            $itemsTable->addCell(1200)->addText('${item_jumlah}', ['size' => 10], ['alignment' => Jc::CENTER]);
            $itemsTable->addCell(4000)->addText('${item_nama}', ['size' => 10]);
            $itemsTable->addCell(1200)->addText('${item_satuan}', ['size' => 10], ['alignment' => Jc::CENTER]);
            $itemsTable->addCell(1400)->addText('${item_harga}', ['size' => 10], ['alignment' => Jc::RIGHT]);
            $itemsTable->addCell(1400)->addText('${item_subtotal}', ['size' => 10], ['alignment' => Jc::RIGHT]);

            // Total Row
            $itemsTable->addRow();
            $itemsTable->addCell(8600, ['bgColor' => 'FFF5F0', 'gridSpan' => 5])->addText('TOTAL', ['bold' => true, 'size' => 11], ['alignment' => Jc::RIGHT]);
            $itemsTable->addCell(1400, ['bgColor' => 'FFF5F0'])->addText('${total}', ['bold' => true, 'size' => 11], ['alignment' => Jc::RIGHT]);

            $section->addTextBreak(2);

            // ==========================================
            // SIGNATURE TABLE
            // ==========================================
            $signatureTable = $section->addTable([
                'borderSize' => 0,
                'cellMargin' => 80,
                'width' => 100 * 50,
            ]);

            $signatureTable->addRow();
            
            // Staff Column
            $staffCell = $signatureTable->addCell(5000);
            $staffCell->addText('Pemohon', ['bold' => true, 'size' => 11], ['alignment' => Jc::CENTER, 'spaceAfter' => 600]);
            $staffCell->addText('${staff_name}', ['bold' => true, 'size' => 10], ['alignment' => Jc::CENTER, 'spaceAfter' => 80]);
            $staffCell->addText('${staff_date}', ['size' => 9, 'color' => '666666'], ['alignment' => Jc::CENTER]);

            // Manager Column
            $managerCell = $signatureTable->addCell(5000);
            $managerCell->addText('Menyetujui', ['bold' => true, 'size' => 11], ['alignment' => Jc::CENTER, 'spaceAfter' => 100]);
            $managerCell->addText('${signature}', [], ['alignment' => Jc::CENTER, 'spaceAfter' => 100]);
            $managerCell->addText('${manager_name}', ['bold' => true, 'size' => 10], ['alignment' => Jc::CENTER, 'spaceAfter' => 80]);
            $managerCell->addText('${manager_date}', ['size' => 9, 'color' => '666666'], ['alignment' => Jc::CENTER]);

            $section->addTextBreak(1);

            // ==========================================
            // FOOTER
            // ==========================================
            $section->addText(
                'Sushi Mentai - Japanese Restaurant',
                ['bold' => true, 'size' => 10, 'color' => 'F97316'],
                ['alignment' => Jc::CENTER, 'spaceAfter' => 80]
            );
            $section->addText(
                'Dokumen ini dihasilkan secara otomatis oleh sistem PR Generator',
                ['size' => 9, 'color' => '666666'],
                ['alignment' => Jc::CENTER]
            );

            // Save template
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($templatePath);

            $this->info("✅ Template berhasil dibuat di: {$templatePath}");
            $this->info("📄 File size: " . number_format(filesize($templatePath) / 1024, 2) . " KB");
            
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            return 1;
        }
    }
}