<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $slipData['employee_name'] }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            vertical-align: top;
            width: 50%;
        }
        .info-label {
            width: 100px;
            display: inline-block;
            color: #666;
        }
        .info-value {
            font-weight: bold;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .salary-table th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .salary-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .salary-table .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #fafafa;
            font-weight: bold;
            font-size: 14px;
        }
        .total-row td {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 12px 8px;
        }
        .terbilang {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 30px;
            font-style: italic;
        }
        .signature-table {
            width: 100%;
            margin-top: 50px;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
        }
        .signature-space {
            height: 80px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $slipData['school_name'] }}</h1>
        <h2>SLIP GAJI PEGAWAI</h2>
        <p>Periode: {{ $slipData['period'] }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <div><span class="info-label">Nama</span>: <span class="info-value">{{ $slipData['employee_name'] }}</span></div>
                <div><span class="info-label">NIP</span>: <span class="info-value">{{ $slipData['nip'] ?? '-' }}</span></div>
            </td>
            <td>
                <div><span class="info-label">Status</span>: <span class="info-value">{{ $slipData['employment_status'] }}</span></div>
                <div><span class="info-label">Jam Mengajar</span>: <span class="info-value">{{ $slipData['teaching_hours'] }} jam/minggu</span></div>
            </td>
        </tr>
    </table>

    <table class="salary-table">
        <thead>
            <tr>
                <th>Komponen Gaji</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($slipData['components'] as $component)
            <tr>
                <td>{{ $component['label'] }}</td>
                <td class="text-right">{{ number_format($component['amount'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row" style="background-color: #f0fdf4;">
                <td>PENGHASILAN BRUTO (GROSS PAY)</td>
                <td class="text-right">Rp {{ number_format($slipData['gross_pay'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if(!empty($slipData['deductions']))
    <h3 style="font-size: 11px; text-transform: uppercase; margin-bottom: 5px;">Potongan</h3>
    <table class="salary-table">
        <tbody>
            @foreach($slipData['deductions'] as $deduction)
            <tr>
                <td>{{ $deduction['label'] }}</td>
                <td class="text-right">-{{ number_format($deduction['amount'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row" style="background-color: #fef2f2;">
                <td>TOTAL POTONGAN</td>
                <td class="text-right">Rp ({{ number_format($slipData['total_deductions'], 0, ',', '.') }})</td>
            </tr>
        </tbody>
    </table>
    @endif

    <table class="salary-table">
        <tbody>
            <tr class="total-row" style="font-size: 16px; background-color: #eef2ff;">
                <td>TAKE HOME PAY (THP Netto)</td>
                <td class="text-right">Rp {{ number_format($slipData['take_home_pay'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="terbilang">
        <strong>Terbilang:</strong> {{ $slipData['terbilang'] }}
    </div>

    <table class="signature-table">
        <tr>
            <td>
                <p>Diterima oleh,</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $slipData['employee_name'] }}</p>
            </td>
            <td>
                <p>Mengetahui,</p>
                <div class="signature-space"></div>
                <p class="signature-name">Kepala Sekolah / Bendahara</p>
            </td>
        </tr>
    </table>
</body>
</html>
