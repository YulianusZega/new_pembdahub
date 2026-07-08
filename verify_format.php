<?php
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::where('role', 'siswa')
    ->where('school_id', 1)
    ->orderBy('id', 'desc')
    ->take(10)
    ->get(['id', 'name', 'email']);

echo "Sample SMP Accounts:\n";
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email}\n";
}
