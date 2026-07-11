<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

if (!isset($_GET['token']) || $_GET['token'] !== 'pembda2026clear') {
    die("Akses ditolak.");
}

try {
    DB::table('forum_reactions')->truncate();
    echo "<h1>Sukses!</h1>";
    echo "<p>Tabel forum_reactions berhasil dibersihkan dari karakter emoji rusak.</p>";
} catch (\Exception $e) {
    echo "<h1>Gagal:</h1> <pre>" . $e->getMessage() . "</pre>";
}
