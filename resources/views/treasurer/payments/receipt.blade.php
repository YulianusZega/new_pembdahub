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
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 20px;
            color: #047857;
            margin-bottom: 3px;
        }
        .header h2 {
            font-size: 15px;
            color: #10b981;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 9px;
            color: #6b7280;
        }
        .receipt-info {
            margin-bottom: 12px;
            background: #f3f4f6;
            padding: 10px;
            border-radius: 5px;
        }
        .receipt-info table {
            width: 100%;
        }
        .receipt-info td {
            padding: 3px 0;
        }
        .receipt-info td:first-child {
            width: 140px;
            font-weight: bold;
            color: #4b5563;
        }
        .section-title {
            background: #10b981;
            color: white;
            padding: 5px 10px;
            margin-top: 12px;
            margin-bottom: 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background: #d1fae5;
            color: #047857;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #6ee7b7;
            font-weight: bold;
            font-size: 11px;
        }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .total-box {
            background: #d1fae5;
            border: 2px solid #10b981;
            padding: 10px;
            margin-top: 12px;
            border-radius: 5px;
        }
        .total-box table {
            width: 100%;
        }
        .total-box td {
            padding: 3px 0;
        }
        .total-box .amount {
            font-size: 18px;
            font-weight: bold;
            color: #047857;
            text-align: right;
        }
        .signature-section {
            margin-top: 20px;
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
            margin-bottom: 40px;
            font-size: 10px;
        }
        .signature-box .name {
            font-weight: bold;
            border-top: 1px solid #333;
            display: inline-block;
            padding-top: 5px;
            min-width: 140px;
            font-size: 11px;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
        .verified-badge {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 9px;
            font-weight: bold;
            margin-left: 8px;
        }
        .pending-badge {
            display: inline-block;
            background: #f59e0b;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 9px;
            font-weight: bold;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>YAYASAN PERGURUAN PEMBDA NIAS</h1>
        <h2>{{ strtoupper($payment->student->school->name ?? 'SEKOLAH') }}</h2>
        <p>Jl. Pelita No.31 Kota Gunungsitoli</p>
        <p style="margin-top: 5px; font-weight: bold; font-size: 11px;">
            KWITANSI PEMBAYARAN
            @if($payment->bill && $payment->bill->academicYear)
                @php
                    $academicYear = $payment->bill->academicYear->year;
                    // Check if year is numeric (e.g., 2025) or already formatted (e.g., "2025/2026")
                    if (is_numeric($academicYear)) {
                        $displayYear = $academicYear . '/' . ($academicYear + 1);
                    } else {
                        $displayYear = $academicYear;
                    }
                @endphp
                - TAHUN AJARAN {{ $displayYear }}
            @endif
        </p>
    </div>

    <!-- Receipt Info -->
    <div class="receipt-info">
        <table>
            <tr>
                <td>No. Kwitansi</td>
                <td>: <strong>{{ $payment->receipt_number ?? 'N/A' }}</strong>
                    @if($payment->is_verified)
                        <span class="verified-badge">✓ TERVERIFIKASI</span>
                    @else
                        <span class="pending-badge">⏳ MENUNGGU VERIFIKASI</span>
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
            <td>{{ $payment->student->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <th>NISN</th>
            <td>{{ $payment->student->nisn ?? '-' }}</td>
        </tr>
        @if($payment->student->classroom)
        <tr>
            <th>Kelas</th>
            <td>{{ $payment->student->classroom->class_name ?? '-' }}</td>
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
                        {{ $payment->bill->paymentType->type_name ?? 'Pembayaran Umum' }}
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
                    @elseif($payment->bill && $payment->bill->year)
                        {{ $payment->bill->year }}
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
                    <strong>Rp {{ number_format($payment->amount_paid ?? 0, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Total Amount -->
    <div class="total-box">
        <table>
            <tr>
                <td><strong>TOTAL PEMBAYARAN</strong></td>
                <td class="amount">Rp {{ number_format($payment->amount_paid ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 9px; color: #6b7280; padding-top: 3px;">
                    Terbilang: <em>{{ ucwords(\App\Helpers\TerbilangHelper::convert($payment->amount_paid ?? 0)) }} Rupiah</em>
                </td>
            </tr>
        </table>
    </div>

    <!-- Notes -->
    @if($payment->notes)
    <div class="section-title">CATATAN</div>
    <p style="padding: 8px; background: #f9fafb; border-radius: 3px; font-size: 10px;">{{ $payment->notes }}</p>
    @endif

    <!-- Verification Info -->
    <div class="section-title">INFORMASI VERIFIKASI</div>
    <table class="data-table">
        <tr>
            <th width="30%">Diproses Oleh</th>
            <td>{{ $payment->processedBy->name ?? 'Bendahara' }} (Bendahara)</td>
        </tr>
        <tr>
            <th>Diverifikasi Oleh</th>
            <td>{{ $payment->verifiedBy->name ?? 'Admin' }} (Super Admin)</td>
        </tr>
        <tr>
            <th>Waktu Verifikasi</th>
            <td>{{ $payment->verified_at ? $payment->verified_at->format('d F Y, H:i') : now()->format('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <th>Status Verifikasi</th>
            <td><strong style="color: #10b981;">✓ Terverifikasi</strong></td>
        </tr>
    </table>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Yang Menerima,</p>
            <div class="name">{{ $payment->processedBy->name ?? 'Bendahara' }}</div>
            <p style="font-size: 9px; margin-top: 3px;">(Bendahara)</p>
        </div>
        <div class="signature-box" style="float: right;">
            <p>Yang Menyerahkan,</p>
            <div class="name">{{ $payment->student->full_name ?? '-' }}</div>
            <p style="font-size: 9px; margin-top: 3px;">(Siswa/Wali Murid)</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Kwitansi ini adalah bukti pembayaran yang sah.</strong></p>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB</p>
        <p>Sistem Informasi PEMBDA Hub - Diproses oleh Bendahara</p>
    </div>
</body>
</html>
