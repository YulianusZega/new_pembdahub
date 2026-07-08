<?php
use App\Models\Student;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$student = Student::where('full_name', 'like', 'Andreas Ilham%')->first();
if ($student) {
    echo "Student Name: {$student->full_name}\n";
    echo "Student User ID: {$student->user_id}\n";
    if ($student->user_id) {
        $user = User::find($student->user_id);
        if ($user) {
            echo "User Name: {$user->name}\n";
            echo "User Email: {$user->email}\n";
            echo "User Created At: {$user->created_at}\n";
        } else {
            echo "User not found in users table!\n";
        }
    }
} else {
    echo "Student not found\n";
}
