<?php

namespace App\Http\Controllers\Yayasan;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\TeachingAssignment;
use App\Models\Schedule;
use App\Models\EducationalCalendar;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProgressInputController extends Controller
{
    /**
     * Tampilkan halaman Progress Input Data untuk akun Yayasan
     */
    public function index(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        $data = $this->getProgressData($academicYearId);

        return view('yayasan.progress_input.index', $data);
    }

    /**
     * Export rekap progress input ke PDF
     */
    public function exportPdf(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        $data = $this->getProgressData($academicYearId);

        $pdf = Pdf::loadView('yayasan.progress_input.pdf', $data)
            ->setPaper('a4', 'landscape');

        $fileName = 'rekap_progress_input_data_' . str_replace('/', '_', $data['currentYear']->year ?? '2026_2027') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Helper untuk menghitung 8 indikator progress pada semua unit sekolah
     */
    private function getProgressData($academicYearId = null)
    {
        // 1. Cari Tahun Pelajaran (Prioritaskan TP 2026/2027 jika tidak ada pilihan)
        if ($academicYearId) {
            $currentYear = AcademicYear::find($academicYearId);
        } else {
            $currentYear = AcademicYear::where('year', 'like', '%2026/2027%')->first();
            if (!$currentYear) {
                $currentYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
            }
        }

        $allYears = AcademicYear::orderBy('year', 'desc')->get();
        $schools = School::schoolsOnly()->where('is_active', true)->orderBy('name')->get();

        $items = [];

        // ════════════════ ITEM 1: DATA SISWA BARU ════════════════
        $item1Schools = [];
        foreach ($schools as $school) {
            // Cek tipe unit (SMP -> kelas 7, SMA/SMK -> kelas 10)
            $isSmp = stripos($school->type, 'SMP') !== false || stripos($school->name, 'SMP') !== false;
            $targetGrades = $isSmp ? [7] : [10];
            $gradeLabel = $isSmp ? 'VII SMP' : 'X SMA/SMK';

            // Hitung dari StudentClass di tahun ajaran bersangkutan
            $countFromClass = 0;
            if ($currentYear) {
                $countFromClass = StudentClass::where('academic_year_id', $currentYear->id)
                    ->whereHas('classroom', function ($q) use ($school, $targetGrades) {
                        $q->where('school_id', $school->id)->whereIn('grade_level', $targetGrades);
                    })
                    ->distinct('student_id')
                    ->count('student_id');
            }

            // Hitung langsung dari tabel Student
            $countFromStudent = Student::where('school_id', $school->id)
                ->where('status', 'aktif')
                ->whereHas('currentClassroom', function ($q) use ($targetGrades) {
                    $q->whereIn('grade_level', $targetGrades);
                })
                ->count();

            $siswaBaru = max($countFromClass, $countFromStudent);

            if ($siswaBaru == 0) {
                $rekomendasi = "Belum ada data siswa baru kelas {$gradeLabel} yang masuk ke rombel. Segera proses data PSB/PPDB atau import Excel.";
                $statusColor = 'red';
            } else {
                $rekomendasi = "Data siswa kelas {$gradeLabel} telah terinput sebanyak {$siswaBaru} siswa. Pastikan seluruh siswa baru sudah masuk dalam rombongan belajar.";
                $statusColor = 'green';
            }

            $item1Schools[] = [
                'school_name' => $school->name,
                'perkembangan' => "{$siswaBaru} Siswa",
                'satuan' => 'Siswa',
                'rekomendasi' => $rekomendasi,
                'status_color' => $statusColor,
                'raw_value' => $siswaBaru,
            ];
        }
        $items[] = [
            'number' => 1,
            'title' => 'Data Siswa Baru',
            'description' => 'Jumlah siswa kelas VII SMP dan X SMA/SMK yang sudah masuk dalam satuan siswa',
            'schools_data' => $item1Schools,
        ];

        // ════════════════ ITEM 2: FINALISASI PROFILE GURU ════════════════
        $item2Schools = [];
        foreach ($schools as $school) {
            $teachers = Teacher::where('school_id', $school->id)->get();
            if ($teachers->isEmpty()) {
                $teachers = Employee::where('school_id', $school->id)
                    ->where('employee_type', 'guru')
                    ->where('is_active', true)
                    ->get();
            }

            $totalGuru = $teachers->count();
            $completeGuru = 0;

            foreach ($teachers as $t) {
                $hasId = !empty($t->nik) || !empty($t->nuptk) || !empty($t->teacher_code) || !empty($t->employee_code);
                $hasBirth = !empty($t->birth_place) && !empty($t->birth_date);
                $hasEdu = !empty($t->education_level) || !empty($t->last_education);
                $hasPhone = !empty($t->phone) || !empty($t->phone_number);

                if ($hasId && $hasBirth && $hasEdu && $hasPhone) {
                    $completeGuru++;
                }
            }

            $pct = $totalGuru > 0 ? round(($completeGuru / $totalGuru) * 100, 1) : 0;

            if ($totalGuru == 0) {
                $rekomendasi = "Belum ada data guru terdaftar di unit ini. Segera tambahkan data master guru/pegawai.";
                $statusColor = 'red';
            } elseif ($pct < 50) {
                $rekomendasi = "Presentase kelengkapan profil guru masih rendah ({$pct}%). Himbau guru untuk melengkapi NIK, NUPTK, dan riwayat pendidikan.";
                $statusColor = 'red';
            } elseif ($pct < 100) {
                $sisa = $totalGuru - $completeGuru;
                $rekomendasi = "Kelengkapan mencapai {$pct}% ({$completeGuru} dari {$totalGuru} guru). Koordinasikan kepada {$sisa} guru yang datanya belum lengkap.";
                $statusColor = 'amber';
            } else {
                $rekomendasi = "Sangat baik! Seluruh profil data guru ({$totalGuru} orang) telah lengkap terisi 100%.";
                $statusColor = 'green';
            }

            $item2Schools[] = [
                'school_name' => $school->name,
                'perkembangan' => "{$pct}% ({$completeGuru}/{$totalGuru} Guru Lengkap)",
                'satuan' => 'Presentase (%)',
                'rekomendasi' => $rekomendasi,
                'status_color' => $statusColor,
                'raw_value' => $pct,
            ];
        }
        $items[] = [
            'number' => 2,
            'title' => 'Finalisasi Profile Guru',
            'description' => 'Presentase Profile Data Guru yang sudah lengkap terisi dengan yang masih kosong',
            'schools_data' => $item2Schools,
        ];

        // ════════════════ ITEM 3: FINALISASI PROFILE SISWA ════════════════
        $item3Schools = [];
        foreach ($schools as $school) {
            $students = Student::where('school_id', $school->id)->where('status', 'aktif')->get();
            $totalSiswa = $students->count();
            $completeSiswa = 0;

            foreach ($students as $s) {
                $hasId = !empty($s->nisn) && !empty($s->nis);
                $hasBirth = !empty($s->birth_place) && !empty($s->birth_date);
                $hasParent = !empty($s->parent_name) || !empty($s->guardian_name);
                $hasAddress = !empty($s->address);

                if ($hasId && $hasBirth && $hasParent && $hasAddress) {
                    $completeSiswa++;
                }
            }

            $pct = $totalSiswa > 0 ? round(($completeSiswa / $totalSiswa) * 100, 1) : 0;

            if ($totalSiswa == 0) {
                $rekomendasi = "Belum ada siswa aktif terdaftar di unit ini. Segera verifikasi data siswa.";
                $statusColor = 'red';
            } elseif ($pct < 50) {
                $rekomendasi = "Presentase kelengkapan profil siswa masih di bawah 50% ({$pct}%). Instruksikan wali kelas memonitor pengisian NISN dan data orang tua.";
                $statusColor = 'red';
            } elseif ($pct < 100) {
                $sisa = $totalSiswa - $completeSiswa;
                $rekomendasi = "Sudah {$completeSiswa} dari {$totalSiswa} siswa lengkap ({$pct}%). Tinggal {$sisa} siswa yang perlu melengkapi NISN dan data wali.";
                $statusColor = 'amber';
            } else {
                $rekomendasi = "Sangat baik! Seluruh profil siswa aktif ({$totalSiswa} orang) telah terisi lengkap 100%.";
                $statusColor = 'green';
            }

            $item3Schools[] = [
                'school_name' => $school->name,
                'perkembangan' => "{$pct}% ({$completeSiswa}/{$totalSiswa} Siswa Lengkap)",
                'satuan' => 'Presentase (%)',
                'rekomendasi' => $rekomendasi,
                'status_color' => $statusColor,
                'raw_value' => $pct,
            ];
        }
        $items[] = [
            'number' => 3,
            'title' => 'Finalisasi Profile Siswa',
            'description' => 'Presentase Profile Data Siswa yang sudah lengkap terisi dan masih kosong',
            'schools_data' => $item3Schools,
        ];

        // ════════════════ ITEM 4: PENUGASAN JABATAN ════════════════
        $item4Schools = [];
        foreach ($schools as $school) {
            $posCount = 0;
            if ($currentYear) {
                $posCount = EmployeePosition::where('academic_year_id', $currentYear->id)
                    ->whereHas('employee', function ($q) use ($school) {
                        $q->where('school_id', $school->id);
                    })
                    ->distinct('employee_id')
                    ->count('employee_id');
            }

            if ($posCount == 0) {
                // Fallback cek posisi aktif pegawai di sekolah ini
                $posCount = EmployeePosition::whereHas('employee', function ($q) use ($school) {
                    $q->where('school_id', $school->id)->where('is_active', true);
                })->distinct('employee_id')->count('employee_id');
            }

            if ($posCount == 0) {
                $rekomendasi = "Belum ada SK penugasan struktural untuk TP. 2026/2027. Segera buat penugasan Kepala Sekolah, Wakil Kepala Sekolah, dan Wali Kelas.";
                $statusColor = 'red';
            } else {
                $rekomendasi = "Terdapat {$posCount} orang telah diberi penugasan jabatan struktural. Pastikan SK Penugasan resmi telah disahkan dan divalidasi.";
                $statusColor = 'green';
            }

            $item4Schools[] = [
                'school_name' => $school->name,
                'perkembangan' => "{$posCount} Orang",
                'satuan' => 'Orang',
                'rekomendasi' => $rekomendasi,
                'status_color' => $statusColor,
                'raw_value' => $posCount,
            ];
        }
        $items[] = [
            'number' => 4,
            'title' => 'Penugasan Jabatan',
            'description' => 'Jumlah penugasan struktural dan tugas tambahan yang sudah dibuat',
            'schools_data' => $item4Schools,
        ];

        // ════════════════ ITEM 5: PENUGASAN MENGAJAR ════════════════
        $item5Schools = [];
        foreach ($schools as $school) {
            $totalJam = 0;
            $taCount = 0;

            if ($currentYear) {
                $taQuery = TeachingAssignment::where('academic_year_id', $currentYear->id)
                    ->whereHas('classroom', function ($q) use ($school) {
                        $q->where('school_id', $school->id);
                    });
                $totalJam = (int) $taQuery->sum('hours_per_week');
                $taCount = $taQuery->count();

                if ($taCount == 0) {
                    $taQuery = TeachingAssignment::where('academic_year_id', $currentYear->id)
                        ->whereHas('teacher', function ($q) use ($school) {
                            $q->where('school_id', $school->id);
                        });
                    $totalJam = (int) $taQuery->sum('hours_per_week');
                    $taCount = $taQuery->count();
                }
            }

            if ($totalJam == 0) {
                $rekomendasi = "Belum ada distribusi jam mengajar mata pelajaran untuk TP. 2026/2027. Segera susun pembagian jam mengajar guru per kelas.";
                $statusColor = 'red';
            } else {
                $rekomendasi = "Total jam mengajar terdistribusi: {$totalJam} Jam dari {$taCount} penugasan. Periksa keseimbangan jam kerja guru sesuai regulasi.";
                $statusColor = 'green';
            }

            $item5Schools[] = [
                'school_name' => $school->name,
                'perkembangan' => "{$totalJam} Jam ({$taCount} Penugasan)",
                'satuan' => 'Jam',
                'rekomendasi' => $rekomendasi,
                'status_color' => $statusColor,
                'raw_value' => $totalJam,
                'ta_count' => $taCount,
            ];
        }
        $items[] = [
            'number' => 5,
            'title' => 'Penugasan Mengajar',
            'description' => 'Jumlah jam mengajar dan distribusi beban mata pelajaran yang sudah dibuat',
            'schools_data' => $item5Schools,
        ];

        // ════════════════ ITEM 6: JADWAL PELAJARAN ════════════════
        $item6Schools = [];
        foreach ($schools as $index => $school) {
            $totalTa = $item5Schools[$index]['ta_count'] ?? 0;
            $plottedCount = 0;

            if ($currentYear) {
                $plottedCount = Schedule::where('school_id', $school->id)
                    ->where('academic_year_id', $currentYear->id)
                    ->whereNotNull('teaching_assignment_id')
                    ->distinct('teaching_assignment_id')
                    ->count('teaching_assignment_id');

                if ($plottedCount == 0) {
                    $plottedCount = Schedule::where('school_id', $school->id)
                        ->where('academic_year_id', $currentYear->id)
                        ->count();
                }
            }

            $pct = $totalTa > 0 ? round(($plottedCount / $totalTa) * 100, 1) : ($plottedCount > 0 ? 100 : 0);

            if ($totalTa == 0) {
                $rekomendasi = "Buat penugasan mengajar terlebih dahulu agar dapat diplot ke dalam jadwal pelajaran mingguan.";
                $statusColor = 'red';
            } elseif ($pct == 0) {
                $rekomendasi = "Jadwal pelajaran belum disusun (0%). Segera lakukan plotting hari, jam pelajaran, dan ruangan untuk penugasan mengajar.";
                $statusColor = 'red';
            } elseif ($pct < 100) {
                $sisa = max(0, $totalTa - $plottedCount);
                $rekomendasi = "Jadwal baru terplot {$pct}%. Lanjutkan plotting untuk sisa {$sisa} penugasan agar jadwal pelajaran mingguan siap digunakan.";
                $statusColor = 'amber';
            } else {
                $rekomendasi = "Sangat baik! Seluruh penugasan mengajar ({$totalTa} item) telah 100% terplot ke dalam jadwal pelajaran mingguan.";
                $statusColor = 'green';
            }

            $item6Schools[] = [
                'school_name' => $school->name,
                'perkembangan' => "{$pct}% ({$plottedCount}/{$totalTa} Terplot)",
                'satuan' => 'Presentase (%)',
                'rekomendasi' => $rekomendasi,
                'status_color' => $statusColor,
                'raw_value' => $pct,
            ];
        }
        $items[] = [
            'number' => 6,
            'title' => 'Jadwal Pelajaran',
            'description' => 'Presentase penugasan mengajar yang sudah diplot dalam jadwal pelajaran mingguan',
            'schools_data' => $item6Schools,
        ];

        // ════════════════ ITEM 7: KALENDER PENDIDIKAN ════════════════
        $item7Schools = [];
        foreach ($schools as $school) {
            $kaldikCount = 0;
            if ($currentYear) {
                $kaldikCount = EducationalCalendar::where('academic_year_id', $currentYear->id)
                    ->where(function ($q) use ($school) {
                        $q->where('school_id', $school->id)->orWhereNull('school_id');
                    })
                    ->count();
            }

            if ($kaldikCount == 0) {
                $rekomendasi = "Belum ada agenda kegiatan atau hari libur pada Kalender Pendidikan TP. 2026/2027. Segera input agenda akademik tahunan.";
                $statusColor = 'red';
            } else {
                $rekomendasi = "Kalender pendidikan telah diisi dengan {$kaldikCount} agenda kegiatan. Pastikan jadwal ujian PTS/PAS dan libur semester sudah tercakup.";
                $statusColor = 'green';
            }

            $item7Schools[] = [
                'school_name' => $school->name,
                'perkembangan' => "{$kaldikCount} Data Kegiatan",
                'satuan' => 'Data',
                'rekomendasi' => $rekomendasi,
                'status_color' => $statusColor,
                'raw_value' => $kaldikCount,
            ];
        }
        $items[] = [
            'number' => 7,
            'title' => 'Kalender Pendidikan',
            'description' => 'Jumlah data kegiatan dan agenda yang sudah diisi dalam kalender pendidikan',
            'schools_data' => $item7Schools,
        ];

        // ════════════════ ITEM 8: TAGIHAN SISWA ════════════════
        $item8Schools = [];
        foreach ($schools as $school) {
            $feeCount = PaymentType::where('school_id', $school->id)
                ->where('is_active', true)
                ->count();

            $feeValuedCount = PaymentType::where('school_id', $school->id)
                ->where('is_active', true)
                ->where('amount', '>', 0)
                ->count();

            if ($feeCount == 0) {
                $rekomendasi = "Belum ada master jenis tagihan siswa (SPP, DSP, Ujian, dll). Segera buat jenis tagihan dan tentukan nominal pembayarannya.";
                $statusColor = 'red';
            } elseif ($feeValuedCount < $feeCount) {
                $sisa = $feeCount - $feeValuedCount;
                $rekomendasi = "Terdapat {$feeCount} jenis tagihan, namun {$sisa} jenis belum diberi nilai nominal (> Rp 0). Lengkapi besaran nominalnya.";
                $statusColor = 'amber';
            } else {
                $rekomendasi = "Terdapat {$feeCount} jenis tagihan siswa yang siap digunakan dan seluruhnya telah memiliki nilai nominal.";
                $statusColor = 'green';
            }

            $item8Schools[] = [
                'school_name' => $school->name,
                'perkembangan' => "{$feeCount} Jenis ({$feeValuedCount} Bernilai)",
                'satuan' => 'Jenis',
                'rekomendasi' => $rekomendasi,
                'status_color' => $statusColor,
                'raw_value' => $feeCount,
            ];
        }
        $items[] = [
            'number' => 8,
            'title' => 'Tagihan Siswa',
            'description' => 'Jenis tagihan siswa yang sudah dibuat dan diberi nilai nominal pembayaran',
            'schools_data' => $item8Schools,
        ];

        return [
            'currentYear' => $currentYear,
            'allYears' => $allYears,
            'schools' => $schools,
            'items' => $items,
        ];
    }
}
