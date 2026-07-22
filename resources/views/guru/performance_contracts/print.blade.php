<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Pakta Integritas</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 40px;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .kop-surat h2, .kop-surat h3, .kop-surat h4 {
            margin: 0;
            font-weight: bold;
        }
        .title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 30px;
        }
        .content {
            font-size: 12pt;
            text-align: justify;
        }
        .signature-box {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box table {
            width: 100%;
            text-align: center;
        }
        .signature-box td {
            width: 50%;
            padding-bottom: 80px; /* Space for signature and materai */
            vertical-align: bottom;
        }
        .materai {
            border: 1px dashed #000;
            display: inline-block;
            padding: 10px;
            font-size: 8pt;
            color: #666;
            margin-bottom: 10px;
        }
        @media print {
            body { padding: 0; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; cursor: pointer; border: none; margin-bottom: 20px;">🖨️ Cetak Dokumen</button>

    <div class="kop-surat">
        <h3>YAYASAN PERGURUAN PEMBDA NIAS</h3>
        <h2>{{ $contract->school->name ?? 'SMKS PEMBDA NIAS' }}</h2>
        <p style="font-size: 10pt; margin: 5px 0 0 0;">Alamat: Jl. Pendidikan No. 1, Nias. Email: info@perguruanpembda.com</p>
    </div>

    <div class="title">
        PAKTA INTEGRITAS & PERJANJIAN KINERJA<br>
        TAHUN PELAJARAN {{ $contract->academicYear->year }}
    </div>

    <div class="content">
        <p>Saya yang bertanda tangan di bawah ini:</p>
        <table style="margin-left: 20px; margin-bottom: 20px;">
            <tr><td width="150">Nama</td><td>: <strong>{{ $contract->employee->full_name }}</strong></td></tr>
            <tr><td>NIP / NUPTK</td><td>: {{ $contract->employee->nip ?? '-' }}</td></tr>
            <tr><td>Unit Kerja</td><td>: {{ $contract->school->name ?? 'SMK' }}</td></tr>
            <tr>
                <td>Tipe Kontrak</td>
                <td>: 
                    @if($contract->contract_type == 'pkg_kejuruan') PKG Guru Kejuruan (Form 2A)
                    @elseif($contract->contract_type == 'pkg_umum') PKG Guru Mapel Umum (Form 2B)
                    @else Perjanjian Kinerja Jabatan (Form 4)
                    @endif
                </td>
            </tr>
            @if($contract->contract_type == 'jabatan_tambahan')
            <tr><td>Menjabat Sebagai</td><td>: <strong>{{ $contract->position->position_name ?? '-' }}</strong></td></tr>
            @endif
        </table>

        <p>Dengan ini menyatakan komitmen dan kesanggupan untuk mencapai target kinerja berikut selama Tahun Pelajaran {{ $contract->academicYear->year }}:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;" border="1">
            @if(in_array($contract->contract_type, ['pkg_kejuruan', 'pkg_umum']))
                <thead>
                    <tr>
                        <th style="padding: 8px; text-align: center; background-color: #f8fafc;" width="5%">No</th>
                        <th style="padding: 8px; text-align: left; background-color: #f8fafc;" width="35%">Pilar Perjanjian Kinerja</th>
                        <th style="padding: 8px; text-align: left; background-color: #f8fafc;" width="60%">Rencana Bukti Fisik Nyata (Target)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 8px; text-align: center;">1</td>
                        <td style="padding: 8px;"><strong>{{ $contract->contract_type == 'pkg_kejuruan' ? 'Kompetensi Praktik (30%)' : 'Kompetensi Relevansi Praktik (30%)' }}</strong></td>
                        <td style="padding: 8px;">{{ $contract->target_data['pilar_1'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; text-align: center;">2</td>
                        <td style="padding: 8px;"><strong>{{ $contract->contract_type == 'pkg_kejuruan' ? 'Kontribusi Program (30%)' : 'Kontribusi Program/TEFA (30%)' }}</strong></td>
                        <td style="padding: 8px;">{{ $contract->target_data['pilar_2'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; text-align: center;">3</td>
                        <td style="padding: 8px;"><strong>Kolaborasi (20%)</strong></td>
                        <td style="padding: 8px;">{{ $contract->target_data['pilar_3'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; text-align: center;">4</td>
                        <td style="padding: 8px;"><strong>Budaya Industri 5R (20%)</strong></td>
                        <td style="padding: 8px;">{{ $contract->target_data['pilar_4'] ?? '-' }}</td>
                    </tr>
                </tbody>
            @elseif($contract->contract_type == 'jabatan_tambahan')
                <thead>
                    <tr>
                        <th style="padding: 8px; text-align: center; background-color: #f8fafc;" width="5%">No</th>
                        <th style="padding: 8px; text-align: left; background-color: #f8fafc;" width="95%">Deskripsi Target Pekerjaan (Harus Bisa Diukur)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 8px; text-align: center;">1</td>
                        <td style="padding: 8px;">{{ $contract->target_data['target_1'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; text-align: center;">2</td>
                        <td style="padding: 8px;">{{ $contract->target_data['target_2'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; text-align: center;">3</td>
                        <td style="padding: 8px;">{{ $contract->target_data['target_3'] ?? '-' }}</td>
                    </tr>
                </tbody>
            @endif
        </table>

        <p>Apabila saya terbukti tidak sungguh-sungguh atau gagal mencapai target komitmen yang tertuang di atas, maka saya <strong>bersedia dicabut penugasan mengajar/jabatan saya</strong> pada semester berikutnya, serta menerima sanksi administratif sesuai peraturan Yayasan Perguruan Pembda Nias.</p>
        <p>Demikian Pakta Integritas ini saya buat dengan penuh kesadaran dan tanpa paksaan dari pihak mana pun.</p>
    </div>

    <div class="signature-box">
        <table>
            <tr>
                <td>
                    Mengetahui,<br>
                    Kepala Sekolah
                    <br><br><br><br><br>
                    <strong>_________________________</strong>
                </td>
                <td>
                    Gunungsitoli, {{ date('d F Y') }}<br>
                    Yang Membuat Pernyataan,
                    <br><br>
                    <div class="materai">Materai<br>Rp. 10.000</div>
                    <br><br>
                    <strong>{{ $contract->employee->full_name }}</strong>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
