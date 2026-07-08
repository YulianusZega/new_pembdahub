<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Subject;

$map = [
    'Matematika' => 'MTK',
    'Bahasa Indonesia' => 'BIND',
    'Pendidikan Pancasila' => 'PPKN',
    'Bahasa Inggris' => 'BING',
    'Pendidikan Jasmani, Olahraga dan Kesehatan' => 'PJOK',
    'Seni Budaya' => 'SBD',
    'Sejarah' => 'SEJ',
    'Informatika' => 'INFO',
    'BP/BK' => 'BK',
    'Dasar-dasar Program Keahlian' => 'DDPK',
    'Kosentrasi Keahlian TE' => 'KK-TE',
    'Kosentrasi Keahlian TKR' => 'KK-TKR',
    'Kosentrasi Keahlian TSM' => 'KK-TSM',
    'Kosentrasi Keahlian TJKT' => 'KK-TJKT',
    'Praktik Kerja Lapangan (PKL)' => 'PKL',
    'Koding dan Kecerdasan Artifisial (KKA)' => 'KKA',
    'Mata Pelajaran Pilihan' => 'PILL',
    'Pendidikan Agama dan Budi Pekerti (Katolik)' => 'PA-K',
    'Pendidikan Agama dan Budi Pekerti (Kristen)' => 'PA-P',
    'Pendidikan Agama dan Budi Pekerti (Islam)' => 'PA-I',
];

echo "Updating missing subject codes for School 3...\n";
$subjects = Subject::where('school_id', 3)->whereNull('code')->get();

$count = 0;
foreach ($subjects as $s) {
    if (isset($map[$s->name])) {
        $s->code = $map[$s->name];
        $s->save();
        $count++;
    }
}

echo "Updated $count subjects.\n";
