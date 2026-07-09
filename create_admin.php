<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::firstOrNew(['email' => 'yulzega@gmail.com']);
$user->name = 'Yulianus Zega';
$user->password = bcrypt('PembdaHUB2026');
$user->role = 'superadmin';
$user->is_active = true;
$user->must_change_password = false;
$user->save();

echo "User created successfully.\n";
