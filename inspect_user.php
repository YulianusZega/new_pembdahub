<?php
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = User::find(6);
if ($u) {
    echo "User ID: 6\n";
    echo "Name: {$u->name}\n";
    echo "Username: '{$u->username}'\n";
    echo "Email: {$u->email}\n";
    echo "Role: {$u->role}\n";
    echo "Created At: {$u->created_at}\n";
} else {
    echo "User 6 not found\n";
}
