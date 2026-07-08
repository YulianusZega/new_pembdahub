<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pendaftar PSB</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DAFTAR PENDAFTAR PSB - YAYASAN PEMBDA NIAS</h2>
        <p>Tahun Ajaran: {{ $academicYear->year ?? 'Semua' }} | Unit: {{ $school->name ?? 'Semua' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Reg</th>
                <th>Nama Lengkap</th>
                <th>L/P</th>
                <th>NISN</th>
                <th>Asal Sekolah</th>
                <th>Program/Konsentrasi</th>
                <th>Status</th>
                <th>Tgl Daftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applicants as $index => $a)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $a->registration_number }}</td>
                <td>{{ $a->full_name }}</td>
                <td>{{ $a->gender }}</td>
                <td>{{ $a->nisn }}</td>
                <td>{{ $a->previous_school }}</td>
                <td>
                    {{ $a->school->type === 'SMK' ? ($a->programKeahlian->nama ?? '-') . ' / ' . ($a->konsentrasiKeahlian->nama ?? '-') : $a->school->name }}
                </td>
                <td>{{ $a->getStatusLabel() }}</td>
                <td>{{ $a->submission_date ? $a->submission_date->format('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
