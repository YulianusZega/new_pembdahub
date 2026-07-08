<?php
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::where('role', 'siswa')
    ->where('school_id', 3)
    ->orderBy('name', 'asc')
    ->take(10)
    ->get(['id', 'name', 'email']);

echo "Sample SMK Accounts:\n";
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email}\n";
}

$duplicates = User::where('role', 'siswa')
    ->where('school_id', 3)
    ->where('email', 'like', '%1@smk.pembdahub.com')
    ->take(5)
    ->get(['name', 'email']);

if ($duplicates->count() > 0) {
    echo "\nDetected Duplicates (handled with suffix):\n";
    foreach ($duplicates as $d) {
        echo "Name: {$d->name} | Email: {$d->email}\n";
    }
}
