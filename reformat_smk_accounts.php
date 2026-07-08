<?php
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::beginTransaction();

try {
    echo "🔄 Memperbaiki format email untuk siswa SMK (School ID: 3)...\n";

    $students = Student::where('school_id', 3)->with('user')->get();
    $totalStudents = $students->count();
    $updatedCount = 0;
    $passwordHash = Hash::make('siswasmks');
    $domain = 'smk.pembdahub.com';

    // Track used emails in this batch to handle duplicates accurately in memory
    $usedEmails = [];

    foreach ($students as $student) {
        $user = $student->user;
        if (!$user) {
            // Jika ada siswa SMK tanpa user, kita buatkan akunnya sekalian supaya sinkron
            $fullName = trim($student->full_name);
            $nameParts = explode(' ', $fullName);
            $firstName = strtolower($nameParts[0]);
            $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);

            $newEmail = $firstName . '@' . $domain;
            $counter = 1;
            while (in_array($newEmail, $usedEmails) || User::where('email', $newEmail)->exists()) {
                $newEmail = $firstName . $counter . '@' . $domain;
                $counter++;
            }
            $usedEmails[] = $newEmail;

            $user = User::create([
                'name' => $student->full_name,
                'email' => $newEmail,
                'password' => $passwordHash,
                'role' => 'siswa',
                'school_id' => 3,
                'is_active' => true,
                'must_change_password' => true
            ]);
            
            $student->update(['user_id' => $user->id]);
            $updatedCount++;
            continue;
        }

        // Update user yang sudah ada
        $fullName = trim($student->full_name);
        $nameParts = explode(' ', $fullName);
        $firstName = strtolower($nameParts[0]);
        $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);

        $newEmail = $firstName . '@' . $domain;
        
        $counter = 1;
        while (in_array($newEmail, $usedEmails) || User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
            $newEmail = $firstName . $counter . '@' . $domain;
            $counter++;
        }

        $usedEmails[] = $newEmail;

        $user->update([
            'email' => $newEmail,
            'password' => $passwordHash,
            'must_change_password' => true
        ]);

        $updatedCount++;
    }

    DB::commit();
    echo "✅ Berhasil memperbarui/membuat {$updatedCount} akun siswa SMK.\n";
    echo "📧 Format Email baru: [nama depan]@smk.pembdahub.com\n";
    echo "🔑 Password baru: siswasmks\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Terjadi kesalahan: " . $e->getMessage() . "\n";
}
