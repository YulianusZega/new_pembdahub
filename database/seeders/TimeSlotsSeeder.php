<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotsSeeder extends Seeder
{
    public function run(): void
    {
        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            $activeYear = AcademicYear::orderBy('id', 'desc')->first();
        }

        if (!$activeYear) {
            $this->command->error('Tidak ada Tahun Pelajaran aktif. Jalankan seeder TP terlebih dahulu.');
            return;
        }

        $this->command->info("Menggunakan Tahun Pelajaran: {$activeYear->year} (ID: {$activeYear->id})");

        $schools = School::where('is_active', 1)->where('type', '!=', 'yayasan')->get();

        if ($schools->isEmpty()) {
            $this->command->error('Tidak ada sekolah aktif.');
            return;
        }

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($schools as $school) {
            $schoolType = strtoupper($school->type); // SMP, SMA, SMK

            // Hapus slot lama untuk sekolah + TP ini (aman, tidak hapus TP lain)
            TimeSlot::where('school_id', $school->id)
                ->where('academic_year_id', $activeYear->id)
                ->delete();

            $this->command->info("Membuat time slots untuk {$school->name} ({$schoolType})...");

            $slots = $this->getSlotsForSchool($schoolType, $school->id, $activeYear->id);

            $created = 0;
            foreach ($days as $day) {
                foreach ($slots as $slot) {
                    $slot['day_of_week'] = $day;
                    TimeSlot::create($slot);
                    $created++;
                }
            }

            $this->command->info("  → {$created} slots dibuat untuk {$school->name}");
        }

        $this->command->info('✅ Selesai membuat time slots untuk semua sekolah.');
    }

    private function getSlotsForSchool(string $type, int $schoolId, int $yearId): array
    {
        $base = [
            'school_id'        => $schoolId,
            'academic_year_id' => $yearId,
            'is_active'        => true,
        ];

        if ($type === 'SMK') {
            return array_map(fn($s) => array_merge($base, $s), [
                // Upacara / Apel
                ['slot_name' => 'Apel',        'slot_type' => 'ceremony', 'slot_order' => 1,  'start_time' => '06:40', 'end_time' => '07:00', 'duration_minutes' => 20,  'is_teaching_slot' => false],
                ['slot_name' => '5S',          'slot_type' => 'ceremony', 'slot_order' => 2,  'start_time' => '07:00', 'end_time' => '07:15', 'duration_minutes' => 15,  'is_teaching_slot' => false],
                // Les 1–4
                ['slot_name' => 'Les 1',       'slot_type' => 'lesson',   'slot_order' => 3,  'start_time' => '07:15', 'end_time' => '07:58', 'duration_minutes' => 43,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 2',       'slot_type' => 'lesson',   'slot_order' => 4,  'start_time' => '07:58', 'end_time' => '08:41', 'duration_minutes' => 43,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 3',       'slot_type' => 'lesson',   'slot_order' => 5,  'start_time' => '08:41', 'end_time' => '09:24', 'duration_minutes' => 43,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 4',       'slot_type' => 'lesson',   'slot_order' => 6,  'start_time' => '09:24', 'end_time' => '10:07', 'duration_minutes' => 43,  'is_teaching_slot' => true],
                // Istirahat 1
                ['slot_name' => 'Istirahat 1', 'slot_type' => 'break',    'slot_order' => 7,  'start_time' => '10:07', 'end_time' => '10:27', 'duration_minutes' => 20,  'is_teaching_slot' => false],
                // Les 5–7
                ['slot_name' => 'Les 5',       'slot_type' => 'lesson',   'slot_order' => 8,  'start_time' => '10:27', 'end_time' => '11:10', 'duration_minutes' => 43,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 6',       'slot_type' => 'lesson',   'slot_order' => 9,  'start_time' => '11:10', 'end_time' => '11:53', 'duration_minutes' => 43,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 7',       'slot_type' => 'lesson',   'slot_order' => 10, 'start_time' => '11:53', 'end_time' => '12:36', 'duration_minutes' => 43,  'is_teaching_slot' => true],
                // Istirahat 2
                ['slot_name' => 'Istirahat 2', 'slot_type' => 'break',    'slot_order' => 11, 'start_time' => '12:36', 'end_time' => '13:06', 'duration_minutes' => 30,  'is_teaching_slot' => false],
                // Les 8–11
                ['slot_name' => 'Les 8',       'slot_type' => 'lesson',   'slot_order' => 12, 'start_time' => '13:06', 'end_time' => '13:48', 'duration_minutes' => 42,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 9',       'slot_type' => 'lesson',   'slot_order' => 13, 'start_time' => '13:48', 'end_time' => '14:30', 'duration_minutes' => 42,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 10',      'slot_type' => 'lesson',   'slot_order' => 14, 'start_time' => '14:30', 'end_time' => '15:10', 'duration_minutes' => 40,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 11',      'slot_type' => 'lesson',   'slot_order' => 15, 'start_time' => '15:10', 'end_time' => '15:50', 'duration_minutes' => 40,  'is_teaching_slot' => true],
            ]);
        }

        if ($type === 'SMA') {
            return array_map(fn($s) => array_merge($base, $s), [
                ['slot_name' => 'Upacara/Tadarus', 'slot_type' => 'ceremony', 'slot_order' => 1,  'start_time' => '07:00', 'end_time' => '07:15', 'duration_minutes' => 15,  'is_teaching_slot' => false],
                ['slot_name' => 'Les 1',           'slot_type' => 'lesson',   'slot_order' => 2,  'start_time' => '07:15', 'end_time' => '08:00', 'duration_minutes' => 45,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 2',           'slot_type' => 'lesson',   'slot_order' => 3,  'start_time' => '08:00', 'end_time' => '08:45', 'duration_minutes' => 45,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 3',           'slot_type' => 'lesson',   'slot_order' => 4,  'start_time' => '08:45', 'end_time' => '09:30', 'duration_minutes' => 45,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 4',           'slot_type' => 'lesson',   'slot_order' => 5,  'start_time' => '09:30', 'end_time' => '10:15', 'duration_minutes' => 45,  'is_teaching_slot' => true],
                ['slot_name' => 'Istirahat 1',     'slot_type' => 'break',    'slot_order' => 6,  'start_time' => '10:15', 'end_time' => '10:30', 'duration_minutes' => 15,  'is_teaching_slot' => false],
                ['slot_name' => 'Les 5',           'slot_type' => 'lesson',   'slot_order' => 7,  'start_time' => '10:30', 'end_time' => '11:15', 'duration_minutes' => 45,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 6',           'slot_type' => 'lesson',   'slot_order' => 8,  'start_time' => '11:15', 'end_time' => '12:00', 'duration_minutes' => 45,  'is_teaching_slot' => true],
                ['slot_name' => 'Istirahat 2',     'slot_type' => 'break',    'slot_order' => 9,  'start_time' => '12:00', 'end_time' => '12:30', 'duration_minutes' => 30,  'is_teaching_slot' => false],
                ['slot_name' => 'Les 7',           'slot_type' => 'lesson',   'slot_order' => 10, 'start_time' => '12:30', 'end_time' => '13:15', 'duration_minutes' => 45,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 8',           'slot_type' => 'lesson',   'slot_order' => 11, 'start_time' => '13:15', 'end_time' => '14:00', 'duration_minutes' => 45,  'is_teaching_slot' => true],
                ['slot_name' => 'Les 9',           'slot_type' => 'lesson',   'slot_order' => 12, 'start_time' => '14:00', 'end_time' => '14:45', 'duration_minutes' => 45,  'is_teaching_slot' => true],
            ]);
        }

        // SMP (default)
        return array_map(fn($s) => array_merge($base, $s), [
            ['slot_name' => 'Upacara/Tadarus', 'slot_type' => 'ceremony', 'slot_order' => 1,  'start_time' => '07:00', 'end_time' => '07:15', 'duration_minutes' => 15,  'is_teaching_slot' => false],
            ['slot_name' => 'Les 1',           'slot_type' => 'lesson',   'slot_order' => 2,  'start_time' => '07:15', 'end_time' => '07:55', 'duration_minutes' => 40,  'is_teaching_slot' => true],
            ['slot_name' => 'Les 2',           'slot_type' => 'lesson',   'slot_order' => 3,  'start_time' => '07:55', 'end_time' => '08:35', 'duration_minutes' => 40,  'is_teaching_slot' => true],
            ['slot_name' => 'Les 3',           'slot_type' => 'lesson',   'slot_order' => 4,  'start_time' => '08:35', 'end_time' => '09:15', 'duration_minutes' => 40,  'is_teaching_slot' => true],
            ['slot_name' => 'Les 4',           'slot_type' => 'lesson',   'slot_order' => 5,  'start_time' => '09:15', 'end_time' => '09:55', 'duration_minutes' => 40,  'is_teaching_slot' => true],
            ['slot_name' => 'Istirahat 1',     'slot_type' => 'break',    'slot_order' => 6,  'start_time' => '09:55', 'end_time' => '10:15', 'duration_minutes' => 20,  'is_teaching_slot' => false],
            ['slot_name' => 'Les 5',           'slot_type' => 'lesson',   'slot_order' => 7,  'start_time' => '10:15', 'end_time' => '10:55', 'duration_minutes' => 40,  'is_teaching_slot' => true],
            ['slot_name' => 'Les 6',           'slot_type' => 'lesson',   'slot_order' => 8,  'start_time' => '10:55', 'end_time' => '11:35', 'duration_minutes' => 40,  'is_teaching_slot' => true],
            ['slot_name' => 'Istirahat 2',     'slot_type' => 'break',    'slot_order' => 9,  'start_time' => '11:35', 'end_time' => '12:00', 'duration_minutes' => 25,  'is_teaching_slot' => false],
            ['slot_name' => 'Les 7',           'slot_type' => 'lesson',   'slot_order' => 10, 'start_time' => '12:00', 'end_time' => '12:40', 'duration_minutes' => 40,  'is_teaching_slot' => true],
            ['slot_name' => 'Les 8',           'slot_type' => 'lesson',   'slot_order' => 11, 'start_time' => '12:40', 'end_time' => '13:20', 'duration_minutes' => 40,  'is_teaching_slot' => true],
        ]);
    }
}
