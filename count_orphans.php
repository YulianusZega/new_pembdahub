<?php
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orphanCount = User::where('role', 'siswa')
    ->where('school_id', 1)
    ->whereDoesntHave('student')
    ->count();

echo "Orphaned 'siswa' users for SMP: {$orphanCount}\n";

$sample = User::where('role', 'siswa')
    ->where('school_id', 1)
    ->whereDoesntHave('student')
    ->take(5)
    ->get(['id', 'name', 'email']);

foreach ($sample as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email}\n";
}
