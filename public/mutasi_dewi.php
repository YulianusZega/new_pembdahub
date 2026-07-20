<?php
// Script untuk mutasi DEWI JULI SULASTRI ZEGA dari Guru ke Pegawai Yayasan
// SATU KALI PAKAI - Hapus setelah selesai
$secret = $_GET['secret'] ?? '';
if ($secret !== 'pembda99') {
    die('Akses ditolak.');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$dryRun = isset($_GET['dry_run']);

echo "<pre>";
echo "=== MUTASI PEGAWAI: DEWI JULI SULASTRI ZEGA, S.E ===\n";
echo "Mode: " . ($dryRun ? "DRY RUN (simulasi)" : "EKSEKUSI") . "\n\n";

DB::beginTransaction();
try {
    $emp = App\Models\Employee::find(131);
    if (!$emp) {
        throw new Exception("Employee ID 131 tidak ditemukan!");
    }
    
    echo "SEBELUM:\n";
    echo "  Nama: {$emp->full_name}\n";
    echo "  employee_type: {$emp->employee_type}\n";
    echo "  school_id: {$emp->school_id} ({$emp->school->name})\n\n";
    
    $emp->update([
        'employee_type' => 'staff_tu',
        'school_id' => 4,
    ]);
    
    $teacher = App\Models\Teacher::where('employee_id', 131)->first();
    if ($teacher) {
        $teacher->update(['is_active' => false]);
        echo "Teacher record di-nonaktifkan (is_active = false)\n";
    }
    
    if ($dryRun) {
        DB::rollBack();
        echo "\n⚠️  DRY RUN - Tidak ada perubahan yang disimpan.\n";
        echo "Untuk eksekusi, hapus parameter dry_run dari URL.\n";
    } else {
        DB::commit();
        $emp->refresh();
        echo "\nSESUDAH:\n";
        echo "  employee_type: {$emp->employee_type}\n";
        echo "  school_id: {$emp->school_id} ({$emp->school->name})\n";
        echo "\n✅ MUTASI BERHASIL!\n";
    }
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
echo "</pre>";
