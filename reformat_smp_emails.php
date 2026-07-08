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
    echo "🔄 Memperbaiki format email untuk 287 siswa SMP...\n";

    $students = Student::where('school_id', 1)->with('user')->get();
    $updatedCount = 0;
    $passwordHash = Hash::make('siswasmpsp2');
    $domain = 'smpp2.pembdahub.com';

    // Kita kumpulkan email yang sedang digunakan untuk mengecek duplikasi di memory agar akurat
    $usedEmails = [];

    foreach ($students as $student) {
        $user = $student->user;
        if (!$user) {
            echo "⚠️ Siswa {$student->full_name} tidak punya akun user, melewati...\n";
            continue;
        }

        // Ambil nama depan
        $fullName = trim($student->full_name);
        $nameParts = explode(' ', $fullName);
        $firstName = strtolower($nameParts[0]);
        
        // Bersihkan karakter aneh jika ada (hanya huruf/angka)
        $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);

        $newEmail = $firstName . '@' . $domain;
        
        // Logika Unik: jika budi@... sudah ada, pakai budi1@..., budi2@..., dst
        $counter = 1;
        $originalEmail = $newEmail;
        while (in_array($newEmail, $usedEmails) || User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
            $newEmail = $firstName . $counter . '@' . $domain;
            $counter++;
        }

        // Simpan email yang sudah dipakai ke list
        $usedEmails[] = $newEmail;

        // Update User
        $user->update([
            'email' => $newEmail,
            'password' => $passwordHash,
            'must_change_password' => true
        ]);

        $updatedCount++;
    }

    DB::commit();
    echo "✅ Berhasil memperbarui {$updatedCount} akun siswa SMP.\n";
    echo "📧 Format Email baru: [nama depan]@smpp2.pembdahub.com\n";
    echo "🔑 Password tetap: siswasmpsp2\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Terjadi kesalahan: " . $e->getMessage() . "\n";
}
