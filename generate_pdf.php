<?php
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

function markdownToPdf($mdFilePath, $pdfFilePath, $title) {
    if (!file_exists($mdFilePath)) {
        echo "Error: File $mdFilePath not found.\n";
        return;
    }

    $mdContent = file_get_contents($mdFilePath);
    
    // Convert Markdown to HTML
    $htmlContent = Str::markdown($mdContent);
    
    // HTML Template with CSS styling suited for Dompdf
    $htmlTemplate = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>' . htmlspecialchars($title) . '</title>
        <style>
            @page {
                margin: 40px 50px;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 11px;
                line-height: 1.6;
                color: #2d3748;
            }
            h1 {
                font-size: 18px;
                color: #1a365d;
                border-bottom: 2px solid #2b6cb0;
                padding-bottom: 5px;
                margin-bottom: 15px;
                text-align: center;
                text-transform: uppercase;
            }
            h2 {
                font-size: 13px;
                color: #2b6cb0;
                margin-top: 20px;
                border-bottom: 1px solid #e2e8f0;
                padding-bottom: 3px;
            }
            h3 {
                font-size: 11px;
                color: #2d3748;
                margin-top: 12px;
                font-weight: bold;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                margin-bottom: 15px;
            }
            table, th, td {
                border: 1px solid #cbd5e0;
            }
            th {
                background-color: #edf2f7;
                color: #2b6cb0;
                font-weight: bold;
                padding: 6px;
                text-align: left;
                font-size: 10px;
            }
            td {
                padding: 6px;
                vertical-align: top;
                font-size: 10px;
            }
            ul, ol {
                margin-top: 5px;
                margin-bottom: 10px;
                padding-left: 20px;
            }
            li {
                margin-bottom: 4px;
            }
            blockquote {
                background-color: #f7fafc;
                border-left: 4px solid #4299e1;
                margin: 10px 0;
                padding: 8px 12px;
                color: #4a5568;
            }
            hr {
                border: 0;
                border-top: 1px solid #e2e8f0;
                margin: 15px 0;
            }
            pre {
                background-color: #f7fafc;
                border: 1px solid #edf2f7;
                padding: 10px;
                font-family: Courier, monospace;
                white-space: pre-wrap;
                font-size: 9px;
            }
            .page-break {
                page-break-after: always;
            }
        </style>
    </head>
    <body>
        ' . $htmlContent . '
    </body>
    </html>
    ';

    // Load HTML to Dompdf and save
    Pdf::loadHTML($htmlTemplate)
       ->setPaper('a4', 'portrait')
       ->save($pdfFilePath);

    echo "Success: Generated $pdfFilePath\n";
}

// Generate the PDFs
echo "Starting PDF generation...\n";
markdownToPdf(
    __DIR__.'/SURAT_SOSIALISASI_DAN_PELATIHAN_PEMBDAHUB.md',
    __DIR__.'/SURAT_SOSIALISASI_DAN_PELATIHAN_PEMBDAHUB.pdf',
    'Surat Pemberitahuan Pelatihan Pembda Hub'
);

markdownToPdf(
    __DIR__.'/JADWAL_DAN_MATERI_PELATIHAN_PEMBDAHUB.md',
    __DIR__.'/JADWAL_DAN_MATERI_PELATIHAN_PEMBDAHUB.pdf',
    'Jadwal dan Materi Pelatihan Pembda Hub'
);

markdownToPdf(
    __DIR__.'/SURAT_UNDANGAN_KEPALA_SEKOLAH.md',
    __DIR__.'/SURAT_UNDANGAN_KEPALA_SEKOLAH.pdf',
    'Surat Undangan Pelatihan Pembda Hub - Kepala Sekolah'
);

markdownToPdf(
    __DIR__.'/SURAT_UNDANGAN_ADMIN.md',
    __DIR__.'/SURAT_UNDANGAN_ADMIN.pdf',
    'Surat Undangan Pelatihan Pembda Hub - Admin Operator'
);

markdownToPdf(
    __DIR__.'/SURAT_UNDANGAN_BENDAHARA.md',
    __DIR__.'/SURAT_UNDANGAN_BENDAHARA.pdf',
    'Surat Undangan Pelatihan Pembda Hub - Bendahara Keuangan'
);

markdownToPdf(
    __DIR__.'/SURAT_UNDANGAN_GURU.md',
    __DIR__.'/SURAT_UNDANGAN_GURU.pdf',
    'Surat Undangan Pelatihan Pembda Hub - Guru & Wali Kelas'
);

markdownToPdf(
    __DIR__.'/SURAT_UNDANGAN_PEGAWAI.md',
    __DIR__.'/SURAT_UNDANGAN_PEGAWAI.pdf',
    'Surat Undangan Pelatihan Pembda Hub - Staf Pegawai'
);
echo "PDF generation finished.\n";
