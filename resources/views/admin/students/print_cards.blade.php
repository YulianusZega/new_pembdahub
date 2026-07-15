<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Login - {{ $classroom->class_name }}</title>
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
            padding: 10mm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            gap: 10px;
        }
        .card {
            width: 85mm;
            height: 55mm;
            border: 1px solid #000;
            border-radius: 5px;
            padding: 10px;
            box-sizing: border-box;
            background-color: #fff;
            position: relative;
            page-break-inside: avoid;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .header h3 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0 0;
            font-size: 10px;
        }
        .content {
            font-size: 12px;
        }
        .content table {
            width: 100%;
        }
        .content td {
            padding: 3px 0;
            vertical-align: top;
        }
        .content td:first-child {
            width: 35%;
            font-weight: bold;
        }
        .footer {
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
            text-align: center;
            font-size: 10px;
            font-style: italic;
            border-top: 1px dashed #ccc;
            padding-top: 5px;
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
                padding: 0;
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
        <button class="btn" onclick="window.print()">🖨️ Cetak Kartu Sekarang</button>
        <a href="{{ route('admin.students.index', ['classroom_id' => $classroom->id]) }}" class="btn btn-secondary">Kembali</a>
        <p>Gunakan kertas A4. Pastikan margin diset ke "Minimum" atau "None" pada pengaturan printer.</p>
    </div>

    <div class="page">
        @foreach($students as $student)
        <div class="card">
            <div class="header">
                <h3>KARTU LOGIN SISWA</h3>
                <p>Perguruan Pembda</p>
            </div>
            <div class="content">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>: {{ Str::limit($student->full_name, 25) }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>: {{ $classroom->class_name }}</td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td>: <strong>{{ $student->user ? $student->user->username : $student->nisn }}</strong></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>: <strong>Pembda{{ $student->nisn }}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="footer">
                Login di: {{ url('/login') }}
            </div>
        </div>
        @endforeach
    </div>

    <script>
        window.onload = function() {
            // Uncomment baris di bawah jika ingin otomatis print saat halaman dibuka
            // window.print();
        }
    </script>
</body>
</html>
