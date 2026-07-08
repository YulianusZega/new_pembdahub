<?php

// Pastikan parameter secret dikirim dan benar
if (!isset($_GET['secret']) || $_GET['secret'] !== 'pembda99') {
    http_response_code(403);
    die('Akses ditolak: Secret token tidak valid.');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$student = App\Models\Student::where('full_name', 'like', '%abraham%')
    ->orWhere('full_name', 'like', '%abaraham%')
    ->first();

if (!$student) {
    echo "Siswa Abraham tidak ditemukan.\n";
    exit;
}

$classroom = $student->currentClassroom()->first();
if (!$classroom) {
    echo "Siswa Abraham tidak terdaftar di kelas mana pun.\n";
    exit;
}

echo "ANALISIS KELAS SISWA: " . $student->full_name . "\n";
echo "Kelas: " . $classroom->class_name . " (ID: " . $classroom->id . ")\n\n";

$studentsInClass = $classroom->students()->get();
echo "Total siswa di kelas: " . $studentsInClass->count() . "\n";

echo "Daftar Siswa dan Status Kelompok/Tugas Akhir:\n";
foreach ($studentsInClass as $s) {
    if ($s->id === $student->id) {
        echo "- [Self] " . $s->full_name . " (NISN: " . $s->nisn . ")\n";
        continue;
    }
    
    // Check memberships
    $memberships = $s->finalProjectMemberships()->with('finalProject')->get();
    
    if ($memberships->isEmpty()) {
        echo "- [Tersedia] " . $s->full_name . " (NISN: " . $s->nisn . ") -> Belum masuk kelompok mana pun.\n";
    } else {
        echo "- [TIDAK Tersedia] " . $s->full_name . " (NISN: " . $s->nisn . ") -> Terhubung ke:\n";
        foreach ($memberships as $m) {
            $proj = $m->finalProject;
            if ($proj) {
                echo "  * Proyek ID: " . $proj->id . " | Ketua: " . ($proj->student->full_name ?? 'N/A') . " | Status Proyek: " . $proj->status . " | Peran: " . $m->role . " | Judul: " . $proj->title . "\n";
            } else {
                echo "  * Membership tanpa proyek aktif (ID: " . $m->id . ")\n";
            }
        }
    }
}
