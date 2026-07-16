<?php
require __DIR__."/../vendor/autoload.php";
$app = require_once __DIR__."/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$hash = "\$2y\$12\$aX/QkN9Q8LlNAiubE9zL7uD26s0JH38lACtNPhHa9uEMMJ9YRekS.";
echo "Checking Pembda: " . (Illuminate\Support\Facades\Hash::check("Pembda", $hash) ? "YES" : "NO") . "\n";
echo "Checking empty: " . (Illuminate\Support\Facades\Hash::check("", $hash) ? "YES" : "NO") . "\n";
echo "Checking 123456: " . (Illuminate\Support\Facades\Hash::check("123456", $hash) ? "YES" : "NO") . "\n";

