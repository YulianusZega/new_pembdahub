<?php
require __DIR__."/../vendor/autoload.php";
$app = require_once __DIR__."/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$student = App\Models\Student::whereHas("user")->first();
$nisn = $student->nisn;
$username = $student->user->username;
$password = "Pembda" . $nisn;

// Reset it manually!
$student->user->update([
    "password" => Illuminate\Support\Facades\Hash::make($password)
]);

echo "Updated password for $username to $password\n";
$attempt = Illuminate\Support\Facades\Auth::attempt(["username" => $username, "password" => $password]);
echo "Auth attempt: " . ($attempt ? "YES" : "NO") . "\n";

