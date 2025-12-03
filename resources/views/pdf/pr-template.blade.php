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
            margin: 15mm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 190mm;
            margin: 0 auto;
        }

        /* HEADER WITH LOGO */
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #f97316;
            padding-bottom: 15px;
        }

        .header img {
            width: 150px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 20pt;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            color: #f97316;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Status Badge - Positioned top right */
        .status-badge {
            float: right;
            display: inline-block;
            padding: 6px 16px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10pt;
            margin-top: -50px;
        }

        .status-approved {
            background-color: #22c55e;
            color: white;
        }

        .status-paid {
            background-color: #3b82f6;
            color: white;
        }

        /* Address Section with Box */
        .address-section {
            margin-bottom: 20px;
            padding: 12px;
            background-color: #fff5f0;
            border: 2px solid #f97316;
            border-radius: 6px;
        }

        .address-label {
            font-weight: bold;
            font-size: 11pt;
            color: #f97316;
            margin-bottom: 6px;
        }

        .address-content {
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
        }

        /* Info Grid - Horizontal Layout */
        .info-grid {
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .info-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #ddd;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-cell {
            display: table-cell;
            padding: 8px 12px;
            vertical-align: middle;
            border-right: 1px solid #ddd;
        }

        .info-cell:last-child {
            border-right: none;
        }

        .info-label {
            font-weight: bold;
            width: 25%;
            background-color: #fff5f0;
            color: #f97316;
        }

        .info-value {
            width: 25%;
        }

        /* Items Table - Clean Design */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th {
            background-color: #f97316;
            color: white;
            padding: 10px 8px;
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            border: 1px solid #ea580c;
        }

        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 10pt;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .items-table tfoot tr {
            background-color: #fff5f0;
            font-weight: bold;
            border-top: 2px solid #f97316;
        }

        .items-table tfoot td {
            padding: 10px 8px;
            font-size: 11pt;
        }

        .right { text-align: right; }
        .center { text-align: center; }
        .left { text-align: left; }

        /* Payment Info Box */
        .payment-info {
            margin: 25px 0;
            padding: 15px;
            background-color: #eff6ff;
            border: 2px solid #3b82f6;
            border-radius: 6px;
        }

        .payment-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 10px;
            color: #3b82f6;
            text-transform: uppercase;
        }

        .payment-row {
            margin-bottom: 6px;
            font-size: 10pt;
            line-height: 1.6;
        }

        .payment-label {
            display: inline-block;
            width: 35%;
            font-weight: bold;
            color: #1e40af;
        }

        /* Signature Section */
        .signatures {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
            display: table;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 15px;
        }

        .signature-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 60px;
            color: #000;
            text-transform: uppercase;
        }

        .signature-image {
            margin: 15px auto;
            max-height: 80px;
            max-width: 200px;
        }

        .signature-line {
            border-top: 2px solid #000;
            width: 75%;
            margin: 0 auto;
            padding-top: 8px;
            font-weight: bold;
            font-size: 10pt;
        }

        .signature-date {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #f97316;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .footer-company {
            font-weight: bold;
            color: #f97316;
            font-size: 10pt;
            margin-bottom: 5px;
        }

        /* Clearfix */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Header with Logo -->
    <div class="header clearfix">
        @php
            $logoPath = public_path('sushi-mentai-logo.png');
            $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
            $logoMime = file_exists($logoPath) ? mime_content_type($logoPath) : 'image/png';
        @endphp

        @if($logoData)
            <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Sushi Mentai Logo">
        @else
            <div style="text-align: center; font-weight: bold; font-size: 18pt; color: #f97316;">SUSHI MENTAI</div>
        @endif
        
        <div class="title">Purchase Requisition</div>

        <!-- Status Badge (Top Right) -->
        @if($pr->isApproved() || $pr->isPaid())
        <span class="status-badge {{ $pr->isPaid() ? 'status-paid' : 'status-approved' }}">
            {{ $pr->isPaid() ? 'PAID' : 'APPROVED' }}
        </span>
        @endif
    </div>

    <!-- Address Section -->
    <div class="address-section">
        <div class="address-label">Kepada: Head Office</div>
        <div class="address-content">
            Ruko Darwin Barat No.3, Gading Serpong Kel. Medang<br>
            Kec. Pagedangan Kabupaten Tanggerang, Banten 15334
        </div>
    </div>

    <!-- Info Grid -->
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell info-label">Tanggal</div>
            <div class="info-cell info-value">{{ $pr->tanggal->format('d/m/Y') }}</div>
            <div class="info-cell info-label">Perihal</div>
            <div class="info-cell info-value">{{ $pr->perihal }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Alasan</div>
            <div class="info-cell info-value">{{ $pr->alasan ?? '-' }}</div>
            <div class="info-cell info-label">Outlet</div>
            <div class="info-cell info-value">{{ $pr->outlet->name }}</div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:8%;">NO</th>
                <th style="width:12%;">JUMLAH</th>
                <th style="width:40%;">NAMA ITEM</th>
                <th style="width:12%;">SATUAN</th>
                <th style="width:14%;">HARGA</th>
                <th style="width:14%;">SUBTOTAL</th>
            </tr>
        </thead>

        <tbody>
        @foreach($pr->items as $item)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center">{{ $item->jumlah }}</td>
                <td class="left">{{ $item->nama_item }}</td>
                <td class="center">{{ $item->satuan }}</td>
                <td class="right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach

        @php
            $emptyRows = max(0, 4 - $pr->items->count());
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
        <div class="payment-title">Informasi Pembayaran</div>
        
        <div class="payment-row">
            <span class="payment-label">Tanggal Transfer:</span>
            {{ $pr->payment_date->format('d/m/Y') }}
        </div>

        <div class="payment-row">
            <span class="payment-label">Jumlah Transfer:</span>
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
    <div class="signatures">
        <!-- Staff/Creator Signature -->
        <div class="signature-box">
            <div class="signature-title">Pemohon</div>
            
            @if($pr->hasSignature())
                <div style="min-height: 80px;"></div>
            @else
                <div style="min-height: 80px;"></div>
            @endif
            
            <div class="signature-line">
                {{ $pr->creator->name }}
            </div>
            <div class="signature-date">
                {{ $pr->created_at->format('d/m/Y') }}
            </div>
        </div>

        <!-- Manager Signature -->
        <div class="signature-box">
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
                    <div style="min-height: 80px; margin: 10px 0;">
                        <img src="data:{{ $signatureMime }};base64,{{ $signatureData }}" 
                            alt="Manager Signature" 
                            class="signature-image">
                    </div>
                @else
                    <div style="min-height: 80px;"></div>
                @endif
                
                <div class="signature-line">
                    {{ $pr->approver->name }}
                </div>
                <div class="signature-date">
                    {{ $pr->approved_at->format('d/m/Y') }}
                </div>
            @else
                <div style="min-height: 80px;"></div>
                <div class="signature-line">
                    ( ________________________ )
                </div>
                <div class="signature-date">
                    Tanggal: _______________
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-company">Sushi Mentai - Japanese Restaurant</div>
        <div>Dokumen ini dihasilkan secara otomatis oleh sistem PR Generator</div>
        <div style="margin-top: 5px;">Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB</div>
    </div>

</div>
</body>
</html>