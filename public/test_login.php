<?php
require __DIR__."/../vendor/autoload.php";
$app = require_once __DIR__."/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$student = App\Models\Student::whereHas("user")->first();
if ($student) {
    $user = $student->user;
    $username = $user->username;
    $nisn = $student->nisn;
    $password = "Pembda" . $nisn;
    echo "Student Username: " . $username . "\n";
    echo "Expected Password: " . $password . "\n";
    echo "Is Active: " . $user->is_active . "\n";
    echo "Hash match manually: " . (Illuminate\Support\Facades\Hash::check($password, $user->password) ? "YES" : "NO") . "\n";
    echo "Auth attempt: " . (Illuminate\Support\Facades\Auth::attempt(["username" => $username, "password" => $password]) ? "YES" : "NO") . "\n";
}

$teacher = App\Models\Teacher::whereHas("user")->first();
if ($teacher) {
    $user = $teacher->user;
    $username = $user->username;
    $kode = $teacher->kode_guru;
    $password = "Pembda" . $kode;
    echo "Teacher Username: " . $username . "\n";
    echo "Expected Password: " . $password . "\n";
    echo "Is Active: " . $user->is_active . "\n";
    echo "Hash match manually: " . (Illuminate\Support\Facades\Hash::check($password, $user->password) ? "YES" : "NO") . "\n";
    echo "Auth attempt: " . (Illuminate\Support\Facades\Auth::attempt(["username" => $username, "password" => $password]) ? "YES" : "NO") . "\n";
}

