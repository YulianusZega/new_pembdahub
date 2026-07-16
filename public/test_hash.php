<?php
require __DIR__."/../vendor/autoload.php";
$app = require_once __DIR__."/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$student = App\Models\Student::whereHas("user")->first();
if ($student) {
    echo "Student DB Password Hash: " . $student->user->password . "\n";
    echo "Double hash check: " . (Illuminate\Support\Facades\Hash::check(Illuminate\Support\Facades\Hash::make("Pembda" . $student->nisn), $student->user->password) ? "YES" : "NO") . "\n";
}

