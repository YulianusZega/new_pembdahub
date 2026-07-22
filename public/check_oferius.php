<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Schedule;

// Validasi
if (request('confirm') === 'yes') {
    $count = Schedule::where('school_id', 3)->count();
    Schedule::where('school_id', 3)->delete();
    echo "BERHASIL MENGHAPUS " . $count . " JADWAL UNTUK SMKS PEMBDA NIAS.";
} else {
    $count = Schedule::where('school_id', 3)->count();
    echo "SIMULASI: Akan menghapus " . $count . " jadwal di SMKS Pembda Nias. Tambahkan &confirm=yes untuk mengeksekusi.";
}
