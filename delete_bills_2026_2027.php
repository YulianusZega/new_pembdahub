<?php

// Determine execution environment
$isCli = php_sapi_name() === 'cli' || (isset($argv) && count($argv) > 1 && in_array('--cli', $argv));

if (!$isCli) {
    // Web execution security check
    $token = $_GET['token'] ?? null;
    if ($token !== 'pembda2026delete') {
        header('HTTP/1.1 403 Forbidden');
        echo "<h1>403 Forbidden</h1>";
        echo "Akses ditolak. Token keamanan tidak valid.";
        exit;
    }
}

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

// Helper to format output based on environment
function printOutput($message, $isCli) {
    if ($isCli) {
        echo $message . "\n";
    } else {
        echo htmlspecialchars($message) . "<br>";
    }
}

printOutput("=== PROSES PENGHAPUSAN TAGIHAN & PEMBAYARAN TP. 2026/2027 ===", $isCli);

try {
    DB::transaction(function () use ($isCli) {
        // Find Academic Year
        $year = AcademicYear::where('year', 'like', '%2026/2027%')
            ->orWhere('year', 'like', '%2026-2027%')
            ->first();

        if (!$year) {
            printOutput("Tahun Pelajaran TP. 2026/2027 tidak ditemukan di database.", $isCli);
            return;
        }

        printOutput("Tahun Pelajaran ditemukan: ID: {$year->id}, Nama: {$year->year}", $isCli);

        // Get bill IDs first
        $billIds = DB::table('student_bills')
            ->where('academic_year_id', $year->id)
            ->pluck('id')
            ->toArray();

        $billsCount = count($billIds);

        if ($billsCount === 0) {
            printOutput("Tidak ada tagihan (student_bills) yang ditemukan untuk TP. 2026/2027.", $isCli);
            printOutput("Tidak ada data yang dihapus.", $isCli);
            return;
        }

        printOutput("Jumlah tagihan ditemukan: {$billsCount}", $isCli);

        // Delete payments associated with these bills
        $paymentsDeleted = DB::table('payments')
            ->whereIn('bill_id', $billIds)
            ->delete();

        printOutput("Jumlah data pembayaran (payments) yang berhasil dihapus: {$paymentsDeleted}", $isCli);

        // Delete the bills
        $billsDeleted = DB::table('student_bills')
            ->where('academic_year_id', $year->id)
            ->delete();

        printOutput("Jumlah data tagihan (student_bills) yang berhasil dihapus: {$billsDeleted}", $isCli);
        printOutput("Transaksi berhasil diselesaikan (committed).", $isCli);
    });

} catch (\Exception $e) {
    printOutput("ERROR: Terjadi kesalahan saat memproses penghapusan data.", $isCli);
    printOutput($e->getMessage(), $isCli);
}

printOutput("=== PROSES SELESAI ===", $isCli);
if (!$isCli) {
    echo "<br><strong>PENTING:</strong> Segera hapus file <code>delete_bills_2026_2027.php</code> dari hosting Anda sekarang demi keamanan.";
}
