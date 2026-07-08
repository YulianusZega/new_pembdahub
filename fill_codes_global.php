<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Subject;

$map = [
    // SMP/SMA General
    'Matematika' => 'MTK',
    'Bahasa Indonesia' => 'BIND',
    'Pendidikan Pancasila' => 'PPKN',
    'Bahasa Inggris' => 'BING',
    'Pendidikan Jasmani, Olahraga dan Kesehatan' => 'PJOK',
    'Seni Budaya' => 'SBD',
    'Sejarah' => 'SEJ',
    'Informatika' => 'INFO',
    'BP/BK' => 'BK',
    'Fisika' => 'FIS',
    'Kimia' => 'KIM',
    'Biologi' => 'BIO',
    'Ekonomi' => 'EKO',
    'Geografi' => 'GEO',
    'Sosiologi' => 'SOS',
    'Pendidikan Agama Islam' => 'PAI',
    'Pendidikan Agama Kristen' => 'PAK',
    'Pendidikan Agama Katolik' => 'PA-KAT',
    // ... add more if needed
    'Dasar-dasar Program Keahlian' => 'DDPK',
    'Kosentrasi Keahlian TE' => 'KK-TE',
    'Kosentrasi Keahlian TKR' => 'KK-TKR',
    'Kosentrasi Keahlian TSM' => 'KK-TSM',
    'Kosentrasi Keahlian TJKT' => 'KK-TJKT',
    'Praktik Kerja Lapangan (PKL)' => 'PKL',
    'Koding dan Kecerdasan Artifisial (KKA)' => 'KKA',
    'Mata Pelajaran Pilihan' => 'PILL',
    'Kreativitas, Inavasi dan Kewirausahaan (KIK)' => 'KIK',
    'Mulok' => 'MLK',
];

echo "Updating missing subject codes for ALL Schools...\n";
$subjects = Subject::whereNull('code')->get();

$count = 0;
foreach ($subjects as $s) {
    if (isset($map[$s->name])) {
        $s->code = $map[$s->name];
        $s->save();
        $count++;
    } else {
        // Fallback: If no map, just take first 4 letters uppercase
        $s->code = strtoupper(substr(str_replace(' ', '', $s->name), 0, 4));
        $s->save();
        $count++;
    }
}

echo "Updated $count subjects across all units.\n";
