<?php
require __DIR__."/../vendor/autoload.php";
$app = require_once __DIR__."/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$hash = "\$2y\$12\$l8yCiYjlUA9wVQC5vP.IzuHYtEYx9/kEKG7Sp0o0RQOprJHwcXwEi";
echo "siswasmpsp2: " . (Illuminate\Support\Facades\Hash::check("siswasmpsp2", $hash) ? "YES" : "NO") . "\n";

