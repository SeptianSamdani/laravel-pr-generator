<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Purchase Requisition - {{ $pr->pr_number }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            width: 180mm !important;
            margin: 0 auto;
        }

        .container {
            width: 100%;
            padding: 10px 15px;
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 180px;
            margin-bottom: 5px;
        }

        .title {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            margin: 5px 0 20px 0;
            color: #000;
            letter-spacing: 0.5px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 15px;
        }

        .status-approved {
            background-color: #22c55e;
            color: white;
        }

        .status-paid {
            background-color: #3b82f6;
            color: white;
        }

        /* Address Section */
        .address-section {
            margin-bottom: 15px;
        }

        .address-label {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .address-content {
            font-size: 9pt;
            line-height: 1.4;
        }

        /* Info Grid */
        .info-grid {
            margin-bottom: 15px;
        }

        .info-row {
            margin-bottom: 3px;
            font-size: 9pt;
        }

        .info-label {
            display: inline-block;
            font-weight: bold;
            width: 120px;
            vertical-align: top;
        }

        .info-value {
            display: inline-block;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            max-width: 175mm;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
            table-layout: fixed;
            overflow-wrap: break-word;
        }

        .items-table-wrapper {
            transform: scale(0.90);
            transform-origin: top left;
        }


        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 7.5pt !important;
            word-wrap: break-word;
        }

        .items-table th {
            background: #f0f0f0;
            color: #000;
            text-align: center;
            font-weight: bold;
        }

        .items-table tfoot tr {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .right { text-align: right; }
        .center { text-align: center; }

        /* Signature Section */
        .signatures {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            display: inline-block;
            width: 48%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }

        .signature-left {
            float: left;
        }

        .signature-right {
            float: right;
        }

        .signature-title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 50px;
            color: #000;
        }

        .signature-image {
            margin: 15px auto;
            max-height: 70px;
            max-width: 180px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 70%;
            margin: 0 auto;
            padding-top: 5px;
            font-weight: normal;
            color: #000;
            font-size: 9pt;
        }

        .signature-date {
            font-size: 9pt;
            color: #000;
            margin-top: 3px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        /* Payment Info */
        .payment-info {
            margin: 20px 0;
            padding: 12px;
            background-color: #f0f9ff;
            border: 1px solid #3b82f6;
        }

        .payment-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            color: #3b82f6;
        }

        .payment-row {
            margin-bottom: 5px;
            font-size: 9pt;
        }

        .payment-label {
            display: inline-block;
            width: 30%;
            font-weight: bold;
        }

        /* Clear float */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Logo + Title -->
    <div class="header">
        @php
            $logoPath = public_path('sushi-mentai-logo.png');
            $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
            $logoMime = file_exists($logoPath) ? mime_content_type($logoPath) : 'image/png';
        @endphp

        @if($logoData)
            <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Sushi Mentai Logo">
        @else
            <div style="text-align: center; font-weight: bold; font-size: 16pt;">SUSHI MENTAI</div>
        @endif
        
        <div class="title">PURCHASE REQUISITION</div>

        <!-- PR Status -->
        @if($pr->isApproved() || $pr->isPaid())
        <span class="status-badge {{ $pr->isPaid() ? 'status-paid' : 'status-approved' }}">
            {{ $pr->isPaid() ? 'PAID' : 'APPROVED' }}
        </span>
        @endif
    </div>

    <!-- Address Section -->
    <div class="address-section">
        <div class="address-label">Kepada Head Office</div>
        <div class="address-content">
            Ruko Darwin Barat No.3, Gading Serpong Kel. Medang<br>
            Kec. Pagedangan Kabupaten Tanggerang, Banten 15334
        </div>
    </div>

    <!-- Info Grid -->
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">PR Number</span>
            <span class="info-value">{{ $pr->pr_number }}</span>
            <span class="info-label" style="margin-left: 60px;">Perihal</span>
            <span class="info-value">{{ $pr->perihal }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal</span>
            <span class="info-value">{{ $pr->tanggal->format('d/m/Y') }}</span>
            <span class="info-label" style="margin-left: 60px;">Alasan</span>
            <span class="info-value">{{ $pr->alasan ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Outlet</span>
            <span class="info-value">{{ $pr->outlet->name }}</span>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
        <tr>
            <th style="width:6%;">NO</th>
            <th style="width:7%;">JUMLAH</th>
            <th style="width:32%;">NAMA ITEM</th>
            <th style="width:8%;">SATUAN</th>
            <th style="width:18%;">HARGA</th>
            <th style="width:18%;">SUBTOTAL</th>
        </tr>
        </thead>

        <tbody>
        @foreach($pr->items as $item)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center">{{ $item->jumlah }}</td>
                <td>{{ $item->nama_item }}</td>
                <td class="center">{{ $item->satuan }}</td>
                <td class="right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach

        @php
            $emptyRows = max(0, 6 - $pr->items->count());
        @endphp

        @for($i = 0; $i < $emptyRows; $i++)
            <tr>
                <td class="center">{{ $pr->items->count() + $i + 1 }}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        @endfor
        </tbody>

        <tfoot>
        <tr>
            <td colspan="5" class="right"><strong>TOTAL</strong></td>
            <td class="right"><strong>Rp {{ number_format($pr->total, 0, ',', '.') }}</strong></td>
        </tr>
        </tfoot>
    </table>

    <!-- Payment Info (if paid) -->
    @if($pr->isPaid() && $pr->payment_date)
    <div class="payment-info">
        <div class="payment-title">INFORMASI PEMBAYARAN</div>
        
        <div class="payment-row">
            <span class="payment-label">Tanggal Transfer:</span>
            {{ $pr->payment_date->format('d/m/Y') }}
        </div>

        <div class="payment-row">
            <span class="payment-label">Jumlah:</span>
            <strong>Rp {{ number_format($pr->payment_amount, 0, ',', '.') }}</strong>
        </div>

        <div class="payment-row">
            <span class="payment-label">Bank:</span>
            {{ $pr->payment_bank }}
        </div>

        <div class="payment-row">
            <span class="payment-label">No. Rekening:</span>
            {{ $pr->payment_account_number }}
        </div>

        <div class="payment-row">
            <span class="payment-label">Nama Penerima:</span>
            {{ $pr->payment_account_name }}
        </div>
    </div>
    @endif

    <!-- Signatures -->
    <div class="signatures clearfix">
        <!-- Staff/Creator Signature -->
        <div class="signature-box signature-left">
            <div class="signature-title">Pemohon</div>
            <div class="signature-line">
                {{ $pr->creator->name }}
            </div>
            <div class="signature-date">
                {{ $pr->created_at->format('d/m/Y') }}
            </div>
        </div>

        <!-- Manager Signature -->
        <div class="signature-box signature-right">
            <div class="signature-title">Menyetujui</div>
            
            @if($pr->hasSignature())
                @php
                    $signaturePath = $pr->manager_signature_path;
                    
                    if (strpos($signaturePath, 'app/public') !== false) {
                        $fullPath = storage_path($signaturePath);
                    } else {
                        $fullPath = storage_path('app/public/' . $signaturePath);
                    }
                    
                    $signatureData = file_exists($fullPath) ? base64_encode(file_get_contents($fullPath)) : '';
                    $signatureMime = file_exists($fullPath) ? mime_content_type($fullPath) : 'image/png';
                @endphp

                @if($signatureData)
                    <div style="min-height: 70px; margin: 10px 0;">
                        <img src="data:{{ $signatureMime }};base64,{{ $signatureData }}" 
                            alt="Manager Signature" 
                            class="signature-image">
                    </div>
                @else
                    <div style="min-height: 50px;"></div>
                @endif
                
                <div class="signature-line">
                    {{ $pr->approver->name }}
                </div>
                <div class="signature-date">
                    {{ $pr->approved_at->format('d/m/Y') }}
                </div>
            @else
                <div style="min-height: 50px;"></div>
                <div class="signature-line">
                    ( .................................. )
                </div>
                <div class="signature-date">
                    Tanggal: _______________
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>
            <strong>Sushi Mentai</strong> - Japanese Restaurant
        </div>
        <div style="margin-top: 3px;">
            Dokumen ini dihasilkan secara otomatis oleh sistem PR Generator
        </div>
        <div style="margin-top: 3px;">
            Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB
        </div>
    </div>

</div>
</body>
</html>