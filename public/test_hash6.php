<?php
require __DIR__."/../vendor/autoload.php";
$app = require_once __DIR__."/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$hash = "\$2y\$12\$aX/QkN9Q8LlNAiubE9zL7uD26s0JH38lACtNPhHa9uEMMJ9YRekS.";
echo "siswasmks: " . (Illuminate\Support\Facades\Hash::check("siswasmks", $hash) ? "YES" : "NO") . "\n";
echo "siswasmas: " . (Illuminate\Support\Facades\Hash::check("siswasmas", $hash) ? "YES" : "NO") . "\n";
echo "siswasma1: " . (Illuminate\Support\Facades\Hash::check("siswasma1", $hash) ? "YES" : "NO") . "\n";
echo "siswasma2: " . (Illuminate\Support\Facades\Hash::check("siswasma2", $hash) ? "YES" : "NO") . "\n";
echo "pembdahub2026: " . (Illuminate\Support\Facades\Hash::check("pembdahub2026", $hash) ? "YES" : "NO") . "\n";

