<?php
require __DIR__."/../vendor/autoload.php";
$app = require_once __DIR__."/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$students = App\Models\Student::whereHas("user")->limit(5)->get();
foreach($students as $student) {
    $nisn = $student->nisn;
    $username = $student->user->username;
    $password_in_db = $student->user->password;
    $expected1 = "Pembda" . $nisn;
    $expected2 = trim("Pembda" . $nisn);
    echo "Siswa: {$student->full_name}\n";
    echo "Username: $username\n";
    echo "NISN: '$nisn'\n";
    echo "Hash: $password_in_db\n";
    echo "Check1: " . (Illuminate\Support\Facades\Hash::check($expected1, $password_in_db) ? "YES" : "NO") . "\n";
    echo "Check2: " . (Illuminate\Support\Facades\Hash::check($expected2, $password_in_db) ? "YES" : "NO") . "\n";
    echo "Check3 (Pembda + username): " . (Illuminate\Support\Facades\Hash::check("Pembda" . $username, $password_in_db) ? "YES" : "NO") . "\n";
    echo "Check4 (pembda + nisn): " . (Illuminate\Support\Facades\Hash::check("pembda" . $nisn, $password_in_db) ? "YES" : "NO") . "\n";
    echo "Check5 (password default awal?): " . (Illuminate\Support\Facades\Hash::check("password", $password_in_db) ? "YES" : "NO") . "\n";
    echo "Check6 (tanggal lahir): " . (Illuminate\Support\Facades\Hash::check(date('dmY', strtotime($student->date_of_birth ?? 'now')), $password_in_db) ? "YES" : "NO") . "\n";
    echo "-------------------------\n";
}
