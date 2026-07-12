<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Secret token for security
if ($request->get('secret') !== 'pembda99') {
    die('Unauthorized');
}

$alumnis = \App\Models\User::where('role', 'alumni')->latest()->get();

echo "<h1>Daftar Akun Alumni (Untuk Testing)</h1>";
if ($alumnis->isEmpty()) {
    echo "<p>Belum ada alumni yang terdaftar.</p>";
} else {
    echo "<ul>";
    foreach ($alumnis as $alumni) {
        echo "<li><b>Email:</b> {$alumni->email} <br> <b>Nama:</b> {$alumni->name}</li><hr>";
    }
    echo "</ul>";
}
echo "<br><a href='/alumni-register'>Kembali ke Pendaftaran</a>";
