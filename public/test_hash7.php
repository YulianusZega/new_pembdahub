<?php
require __DIR__."/../vendor/autoload.php";
$app = require_once __DIR__."/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$students = App\Models\Student::whereHas("user", function($q) {
    $q->where("password", "!=", "\$2y\$12\$aX/QkN9Q8LlNAiubE9zL7uD26s0JH38lACtNPhHa9uEMMJ9YRekS.");
})->limit(5)->get();

foreach($students as $student) {
    $nisn = $student->nisn;
    $username = $student->user->username;
    $password_in_db = $student->user->password;
    $expected1 = "Pembda" . $nisn;
    echo "Siswa: {$student->full_name}\n";
    echo "Username: $username\n";
    echo "NISN: '$nisn'\n";
    echo "Hash: $password_in_db\n";
    echo "Check1 (Pembda + nisn): " . (Illuminate\Support\Facades\Hash::check($expected1, $password_in_db) ? "YES" : "NO") . "\n";
    echo "Check2 (auth attempt): " . (Illuminate\Support\Facades\Auth::attempt(["username" => $username, "password" => $expected1]) ? "YES" : "NO") . "\n";
    echo "-------------------------\n";
}
