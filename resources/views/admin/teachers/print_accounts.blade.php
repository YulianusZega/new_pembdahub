<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .page {
            background-color: white;
            width: 210mm; /* A4 width */
            min-height: 297mm; /* A4 height */
            padding: 15mm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                padding: 10mm;
                width: 100%;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn" onclick="window.print()">🖨️ Cetak Daftar Sekarang</button>
        <button class="btn btn-secondary" onclick="window.close()">Tutup</button>
        <p>Gunakan kertas A4. Pastikan margin diset ke "Minimum" atau "None" pada pengaturan printer.</p>
    </div>

    <div class="page">
        <div class="header">
            <h3>{{ $title }}</h3>
            <p>Sistem Informasi Perguruan Pembda</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 35%;">Nama Lengkap</th>
                    <th style="width: 20%;">Kode Guru</th>
                    <th style="width: 20%;">Username</th>
                    <th style="width: 20%;">Password (Pola)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teachers as $index => $teacher)
                @php
                    $code = $teacher->teacher_code ?: $teacher->id;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $teacher->full_name }}</td>
                    <td>{{ $teacher->teacher_code }}</td>
                    <td>{{ $teacher->user ? $teacher->user->username : '-' }}</td>
                    <td>Pembda{{ $code }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function() {
            // window.print();
        }
    </script>
</body>
</html>
