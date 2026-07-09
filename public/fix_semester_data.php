<?php
/**
 * Script untuk memindahkan data Nilai, Jadwal, dll dari Semester lama (ID 1)
 * ke Semester TP 2026/2027 Ganjil (ID 7)
 * Akses: https://perguruanpembda.com/fix_semester_data.php?token=pembda99
 */

if (($_GET['token'] ?? '') !== 'pembda99') {
    http_response_code(403);
    die('Akses ditolak.');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

try {
    // 1. Nonaktifkan semua semester dari Tahun Pelajaran lama
    DB::statement("UPDATE semesters SET is_active = 0 WHERE academic_year_id != 5");

    // 2. Pindahkan semua data yang "nyasar" ke semester 1 (Ganjil 25/26) menjadi semester 7 (Ganjil 26/27)
    $tables = [
        'cbt_exams', 'employee_workload_summaries', 'final_grades', 'grades', 
        'lms_courses', 'report_cards', 'schedules', 'student_bills', 
        'student_counseling_records', 'student_development_notes', 
        'student_recommendations', 'teaching_assignments'
    ];

    foreach ($tables as $table) {
        DB::statement("UPDATE {$table} SET semester_id = 7 WHERE semester_id = 1");
    }

    // 3. Bersihkan Cache agar sistem mengenali semester yang baru
    Artisan::call('cache:clear');

    echo "<h1>✅ Sukses Memperbaiki Database!</h1>";
    echo "<p>Semua Nilai Siswa yang tidak muncul tadi sudah berhasil dipindahkan ke Semester TP. 2026/2027.</p>";
    echo "<p>Silakan kembali ke Dashboard Siswa dan refresh halamannya.</p>";

} catch (\Exception $e) {
    echo "<h1>❌ Terjadi Kesalahan:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
