<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi Pembayaran - {{ $payment->receipt_number ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 18px;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 10px;
            color: #6b7280;
        }
        .receipt-info {
            margin-bottom: 20px;
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
        }
        .receipt-info table {
            width: 100%;
        }
        .receipt-info td {
            padding: 5px 0;
        }
        .receipt-info td:first-child {
            width: 150px;
            font-weight: bold;
            color: #4b5563;
        }
        .section-title {
            background: #3b82f6;
            color: white;
            padding: 8px 12px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background: #eff6ff;
            color: #1e40af;
            padding: 10px;
            text-align: left;
            border: 1px solid #bfdbfe;
            font-weight: bold;
        }
        .data-table td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
        }
        .total-box {
            background: #dbeafe;
            border: 2px solid #3b82f6;
            padding: 15px;
            margin-top: 20px;
            border-radius: 8px;
        }
        .total-box table {
            width: 100%;
        }
        .total-box td {
            padding: 5px 0;
        }
        .total-box .amount {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            text-align: right;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
        .signature-box p {
            margin-bottom: 60px;
        }
        .signature-box .name {
            font-weight: bold;
            border-top: 1px solid #333;
            display: inline-block;
            padding-top: 5px;
            min-width: 150px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .verified-badge {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 10px;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PembdaHUB</h1>
        <h2>KWITANSI PEMBAYARAN</h2>
        <p>Jl. Pendidikan No. 123, Jakarta | Tel: (021) 1234567 | Email: info@PembdaHUB.sch.id</p>
    </div>

    <!-- Receipt Info -->
    <div class="receipt-info">
        <table>
            <tr>
                <td>No. Kwitansi</td>
                <td>: <strong>{{ $payment->receipt_number ?? 'N/A' }}</strong>
                    @if($payment->is_verified)
                        <span class="verified-badge"><i class="fas fa-check text-green-500 mr-1"></i> TERVERIFIKASI</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Tanggal Pembayaran</td>
                <td>: {{ $payment->payment_date->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td>No. Referensi</td>
                <td>: {{ $payment->reference_number ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Student Information -->
    <div class="section-title">INFORMASI SISWA</div>
    <table class="data-table">
        <tr>
            <th width="30%">Nama Lengkap</th>
            <td>{{ $payment->student->full_name }}</td>
        </tr>
        <tr>
            <th>NISN</th>
            <td>{{ $payment->student->nisn }}</td>
        </tr>
        @if($payment->student->studentClass)
        <tr>
            <th>Kelas</th>
            <td>{{ $payment->student->studentClass->classroom->class_name ?? '-' }}</td>
        </tr>
        @endif
    </table>

    <!-- Payment Details -->
    <div class="section-title">DETAIL PEMBAYARAN</div>
    <table class="data-table">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th width="40%">Jenis Tagihan</th>
                <th width="20%">Periode</th>
                <th width="20%">Metode</th>
                <th width="20%" style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if($payment->bill)
                        {{ $payment->bill->paymentType->type_name }}
                    @else
                        Pembayaran Umum
                    @endif
                </td>
                <td>
                    @if($payment->bill && $payment->bill->month && $payment->bill->year)
                        @php
                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        {{ $months[$payment->bill->month] }} {{ $payment->bill->year }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @php
                        $methods = [
                            'cash' => 'Tunai',
                            'transfer' => 'Transfer',
                            'qris' => 'QRIS',
                            'card' => 'Kartu Kredit',
                            'check' => 'Cek',
                        ];
                    @endphp
                    {{ $methods[$payment->payment_method] ?? $payment->payment_method }}
                </td>
                <td style="text-align: right;">
                    <strong>Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Total Amount -->
    <div class="total-box">
        <table>
            <tr>
                <td><strong>TOTAL PEMBAYARAN</strong></td>
                <td class="amount">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 10px; color: #6b7280; padding-top: 5px;">
                    Terbilang: <em>{{ ucwords(\App\Helpers\TerbilangHelper::convert($payment->amount_paid)) }} Rupiah</em>
                </td>
            </tr>
        </table>
    </div>

    <!-- Notes -->
    @if($payment->notes)
    <div class="section-title">CATATAN</div>
    <p style="padding: 10px; background: #f9fafb; border-radius: 4px;">{{ $payment->notes }}</p>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Yang Menerima,</p>
            <div class="name">{{ $payment->processedBy->name ?? 'Admin' }}</div>
        </div>
        <div class="signature-box" style="float: right;">
            <p>Yang Menyerahkan,</p>
            <div class="name">{{ $payment->student->full_name }}</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Kwitansi ini adalah bukti pembayaran yang sah.</strong></p>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB</p>
        <p>Sistem Informasi PembdaHUB - Terverifikasi Otomatis</p>
    </div>
</body>
</html>
