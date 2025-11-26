<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Purchase Requisition - {{ $pr->pr_number }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
        }

        .container {
            width: 100%;
            padding: 20px 30px;
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 180px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 20pt;
            font-weight: bold;
            text-align: center;
            margin-top: 5px;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        /* Info box */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 11pt;
        }

        .info-label {
            width: 20%;
            font-weight: bold;
        }

        .info-value {
            width: 80%;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 10pt;
        }

        .items-table th {
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .right { text-align: right; }
        .center { text-align: center; }

        /* Bank / Receiver Info */
        .receiver-box {
            width: 100%;
            border: 1px solid #000;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        .receiver-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 8px;
            text-decoration: underline;
        }

        /* Signature Section */
        .signatures {
            width: 100%;
            margin-top: 40px;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            padding: 20px 10px;
            font-size: 11pt;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            padding-top: 3px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #777;
        }

    </style>
</head>
<body>
<div class="container">

    <!-- Logo + Title -->
    <div class="header">
        <img src="/sushi-mentai-logo.png" alt="Logo">
    </div>

    <div class="title">PURCHASE REQUISITION</div>

    <!-- Info Section -->
    <table class="info-table">
        <tr>
            <td class="info-label">Kepada</td>
            <td class="info-value">
                Head Office<br>
                Ruko Darwin Barat No.3, Gading Serpong Kel. Medang<br>
                Kec. Pagedangan Kabupaten Tanggerang, Banten 15334
            </td>
        </tr>

        <tr>
            <td class="info-label">Tanggal</td>
            <td class="info-value">{{ $pr->tanggal->format('d/m/Y') }}</td>
        </tr>

        <tr>
            <td class="info-label">Perihal</td>
            <td class="info-value">{{ $pr->perihal }}</td>
        </tr>

        <tr>
            <td class="info-label">Alasan</td>
            <td class="info-value">{{ $pr->alasan ?? '-' }}</td>
        </tr>

        <tr>
            <td class="info-label">Outlet</td>
            <td class="info-value">{{ $pr->outlet->name }}</td>
        </tr>
    </table>

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
                <td class="right">{{ number_format($item->harga,0,',','.') }}</td>
                <td class="right">{{ number_format($item->subtotal,0,',','.') }}</td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
        <tr>
            <td colspan="5" class="right"><strong>TOTAL</strong></td>
            <td class="right"><strong>{{ number_format($pr->total,0,',','.') }}</strong></td>
        </tr>
        </tfoot>
    </table>

    <!-- Bank / Receiver Info -->
    <div class="receiver-box">
        <div class="receiver-title">Penerima</div>

        <table style="width:100%; font-size:11pt;">
            <tr><td style="width:25%;">Nama</td><td>{{ $pr->receiver_name ?? '-' }}</td></tr>
            <tr><td>Bank</td><td>{{ $pr->receiver_bank ?? '-' }}</td></tr>
            <tr><td>No. Rekening</td><td>{{ $pr->receiver_account ?? '-' }}</td></tr>
            <tr><td>Phone</td><td>{{ $pr->receiver_phone ?? '-' }}</td></tr>
        </table>
    </div>

    <!-- Signatures -->
    <table class="signatures">
        <tr>
            <td>
                Pemohon:<br>
                <div class="signature-line">{{ $pr->creator->name }}</div>
            </td>

            <td>
                Menyetujui:<br>
                <div class="signature-line">
                    {{ $pr->approver->name ?? '(.....................................)' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y, H:i') }} — Dokumen otomatis dari sistem PR Generator.
    </div>

</div>
</body>
</html>
