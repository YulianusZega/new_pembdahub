<?php
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::beginTransaction();

try {
    // 1. Hapus 31 akun yatim (Siswa SMP tanpa data Student)
    $orphans = User::where('role', 'siswa')
        ->where('school_id', 1)
        ->whereDoesntHave('student')
        ->get();
    
    $orphanCount = $orphans->count();
    foreach ($orphans as $u) {
        $u->delete();
    }
    echo "✓ Berhasil menghapus {$orphanCount} akun siswa yatim (orphaned).\n";

    // 2. Ambil 287 siswa SMP yang belum punya user_id
    $students = Student::where('school_id', 1)
        ->whereNull('user_id')
        ->get();
    
    $studentCount = $students->count();
    $createdCount = 0;
    $password = Hash::make('siswasmpsp2');

    echo "⌛ Menyiapkan pembuatan {$studentCount} akun siswa SMP...\n";

    foreach ($students as $student) {
        // Generate Username: Gunakan NISN (pasti unik)
        $username = $student->nisn;
        
        // Generate Email: nama_depan.nisn@student.smp2pembda.sch.id
        $firstName = strtolower(explode(' ', trim($student->full_name))[0]);
        $email = $firstName . '.' . $student->nisn . '@student.smp2pembda.sch.id';

        // Pastikan email unik di sistem
        $baseEmail = $email;
        $i = 1;
        while (User::where('email', $email)->exists()) {
            $email = $firstName . '.' . $student->nisn . '.' . $i . '@student.smp2pembda.sch.id';
            $i++;
        }

        // Buat User
        $user = User::create([
            'name' => $student->full_name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'siswa',
            'school_id' => 1,
            'is_active' => true,
            'must_change_password' => true, // Opsional: paksa ganti password saat login pertama
        ]);

        // Hubungkan ke Student
        $student->update(['user_id' => $user->id]);
        $createdCount++;
    }

    DB::commit();
    echo "✅ Berhasil membuat {$createdCount} akun user baru untuk siswa SMP.\n";
    echo "🔑 Semua akun menggunakan password: siswasmpsp2\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Terjadi kesalahan: " . $e->getMessage() . "\n";
}
