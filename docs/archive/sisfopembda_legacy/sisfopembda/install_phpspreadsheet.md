# Cara Instalasi dan Integrasi PhpSpreadsheet untuk Export Excel

## 1. Instalasi PhpSpreadsheet

Jalankan perintah berikut di terminal (pastikan Composer sudah terinstall):

```
composer require phpoffice/phpspreadsheet
```

## 2. Contoh Script Export Excel dengan PhpSpreadsheet

Buat file baru, misal: `export_excel_phpspreadsheet.php`

```php
<?php
require 'vendor/autoload.php';
require_once 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil data gaji pegawai (contoh sederhana)
$data = [
    ['Nama', 'Jabatan', 'Tunjangan Jabatan', 'Honor', 'Total Gaji'],
    ['Budi', 'Wali Kelas', 300000, 500000, 800000],
    ['Siti', 'Kasek SMA', 3500000, 0, 3500000],
];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Isi data ke sheet
$rowNum = 1;
foreach ($data as $row) {
    $colNum = 1;
    foreach ($row as $cell) {
        // Format uang: jika 0, kosong; jika >0, pakai Rp dan ribuan
        if (is_numeric($cell) && $colNum > 2) {
            $cell = $cell > 0 ? 'Rp ' . number_format($cell, 0, ',', '.') : '';
        }
        $sheet->setCellValueByColumnAndRow($colNum, $rowNum, $cell);
        $colNum++;
    }
    $rowNum++;
}

// Style header
$sheet->getStyle('A1:E1')->getFont()->setBold(true);
$sheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD700');

// Border semua cell
$sheet->getStyle('A1:E' . ($rowNum-1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

// Output file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Daftar_Gaji_Pegawai.xlsx"');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
```

## 3. Integrasi ke Sistem

- Ganti bagian `$data` dengan query data gaji pegawai dari database.
- Sesuaikan kolom dan style sesuai kebutuhan laporan.
- Panggil file ini dari menu export di aplikasi Anda.

Jika ingin contoh script yang langsung terhubung ke database dan layout sesuai laporan Anda, silakan konfirmasi!
