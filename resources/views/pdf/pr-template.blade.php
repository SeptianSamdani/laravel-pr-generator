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

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }

        .container {
            width: 100%;
            padding: 20px 30px;
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 3px solid #f97316;
        }

        .header img {
            width: 200px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 22pt;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 25px;
            color: #f97316;
            letter-spacing: 1px;
        }

        /* Info Section */
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #fff9f5;
            border-left: 4px solid #f97316;
        }

        .info-row {
            margin-bottom: 8px;
            display: table;
            width: 100%;
        }

        .info-label {
            display: table-cell;
            width: 20%;
            font-weight: bold;
            color: #404040;
            padding-right: 10px;
        }

        .info-value {
            display: table-cell;
            width: 80%;
            color: #262626;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        .items-table th, .items-table td {
            border: 1px solid #d4d4d4;
            padding: 8px;
            font-size: 10pt;
        }

        .items-table th {
            background: #f97316;
            color: white;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .items-table tfoot tr {
            background-color: #fff4ed;
            font-weight: bold;
            font-size: 11pt;
        }

        .items-table tfoot td {
            border-top: 2px solid #f97316;
        }

        .right { text-align: right; }
        .center { text-align: center; }

        /* Signature Section */
        .signatures {
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-box {
            display: inline-block;
            width: 48%;
            text-align: center;
            vertical-align: top;
            padding: 15px;
        }

        .signature-left {
            float: left;
        }

        .signature-right {
            float: right;
        }

        .signature-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 60px;
            color: #404040;
        }

        .signature-image {
            margin: 20px auto;
            max-height: 80px;
            max-width: 200px;
        }

        .signature-line {
            border-top: 2px solid #000;
            width: 80%;
            margin: 0 auto;
            padding-top: 5px;
            font-weight: bold;
            color: #262626;
        }

        .signature-date {
            font-size: 9pt;
            color: #737373;
            margin-top: 5px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e5e5e5;
            text-align: center;
            font-size: 9pt;
            color: #737373;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10pt;
        }

        .status-approved {
            background-color: #22c55e;
            color: white;
        }

        .status-paid {
            background-color: #3b82f6;
            color: white;
        }

        .status-pending {
            background-color: #f59e0b;
            color: white;
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
        <img src="{{ public_path('sushi-mentai-logo.png') }}" alt="Sushi Mentai Logo">
        <div class="title">PURCHASE REQUISITION</div>
    </div>

    <!-- PR Status (if approved/paid) -->
    @if($pr->isApproved() || $pr->isPaid())
    <div style="text-align: center; margin-bottom: 15px;">
        <span class="status-badge {{ $pr->isPaid() ? 'status-paid' : 'status-approved' }}">
            {{ $pr->isPaid() ? 'PAID' : 'APPROVED' }}
        </span>
    </div>
    @endif

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Kepada</div>
            <div class="info-value">
                <strong>Head Office</strong><br>
                Ruko Darwin Barat No.3, Gading Serpong Kel. Medang<br>
                Kec. Pagedangan Kabupaten Tanggerang, Banten 15334
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">PR Number</div>
            <div class="info-value"><strong>{{ $pr->pr_number }}</strong></div>
        </div>

        <div class="info-row">
            <div class="info-label">Tanggal</div>
            <div class="info-value">{{ $pr->tanggal->format('d/m/Y') }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Perihal</div>
            <div class="info-value"><strong>{{ $pr->perihal }}</strong></div>
        </div>

        @if($pr->alasan)
        <div class="info-row">
            <div class="info-label">Alasan</div>
            <div class="info-value">{{ $pr->alasan }}</div>
        </div>
        @endif

        <div class="info-row">
            <div class="info-label">Outlet</div>
            <div class="info-value"><strong>{{ $pr->outlet->name }}</strong></div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
        <tr>
            <th style="width:5%;">No</th>
            <th style="width:10%;">Jumlah</th>
            <th style="width:35%;">Nama Item</th>
            <th style="width:10%;">Satuan</th>
            <th style="width:20%;">Harga</th>
            <th style="width:20%;">Subtotal</th>
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
            // Fill empty rows to make table look consistent (optional)
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
            <td colspan="5" class="right">TOTAL</td>
            <td class="right">Rp {{ number_format($pr->total, 0, ',', '.') }}</td>
        </tr>
        </tfoot>
    </table>

    <!-- Payment Info (if paid) -->
    @if($pr->isPaid() && $pr->payment_date)
    <div class="info-section" style="background-color: #f0f9ff; border-left-color: #3b82f6;">
        <div style="font-weight: bold; font-size: 12pt; margin-bottom: 10px; color: #3b82f6;">
            INFORMASI PEMBAYARAN
        </div>
        
        <div class="info-row">
            <div class="info-label">Tanggal Transfer</div>
            <div class="info-value">{{ $pr->payment_date->format('d/m/Y') }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Jumlah</div>
            <div class="info-value"><strong>Rp {{ number_format($pr->payment_amount, 0, ',', '.') }}</strong></div>
        </div>

        <div class="info-row">
            <div class="info-label">Bank</div>
            <div class="info-value">{{ $pr->payment_bank }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">No. Rekening</div>
            <div class="info-value">{{ $pr->payment_account_number }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Nama Penerima</div>
            <div class="info-value">{{ $pr->payment_account_name }}</div>
        </div>
    </div>
    @endif

    <!-- Signatures -->
    <div class="signatures clearfix">
        <!-- Staff/Creator Signature -->
        <div class="signature-box signature-left">
            <div class="signature-title">Pemohon</div>
            <div style="min-height: 60px;">
                <!-- Staff tidak perlu signature image, hanya nama -->
            </div>
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
                <!-- Display actual signature image -->
                <div style="min-height: 80px; margin: 10px 0;">
                    <img src="{{ storage_path('app/public/' . str_replace('signatures/', '', $pr->manager_signature_path)) }}" 
                         alt="Manager Signature" 
                         class="signature-image">
                </div>
                <div class="signature-line">
                    {{ $pr->approver->name }}
                </div>
                <div class="signature-date">
                    {{ $pr->approved_at->format('d/m/Y') }}
                </div>
            @else
                <!-- Placeholder if not signed yet -->
                <div style="min-height: 60px;">
                    <!-- Empty space for manual signature -->
                </div>
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
            <strong>Sushi Mentai</strong> - Japanese Restaurant<br>
            Dokumen ini dihasilkan secara otomatis oleh sistem PR Generator
        </div>
        <div style="margin-top: 8px;">
            Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB
        </div>
    </div>

</div>
</body>
</html>