<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Classroom;
use App\Models\TeachingAssignment;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class FixDuplicates extends Command
{
    protected $signature = 'app:fix-duplicates';
    protected $description = 'Merapikan dan menghapus Tahun Pelajaran dan Semester yang terduplikat';

    public function handle()
    {
        $this->info("Memulai pembersihan duplikat Tahun Pelajaran...");
        DB::beginTransaction();
        try {
            // Fix Academic Years
            $years = AcademicYear::all()->groupBy('year');
            foreach ($years as $yearName => $group) {
                if ($group->count() > 1) {
                    $keepId = $group->first()->id;
                    $duplicateIds = $group->skip(1)->pluck('id')->toArray();
                    
                    $this->info("Ditemukan duplikat untuk Tahun Pelajaran {$yearName}. Menyimpan ID {$keepId} dan menghapus " . implode(',', $duplicateIds));
                    
                    // Update relations
                    Semester::whereIn('academic_year_id', $duplicateIds)->update(['academic_year_id' => $keepId]);
                    Classroom::whereIn('academic_year_id', $duplicateIds)->update(['academic_year_id' => $keepId]);
                    TeachingAssignment::whereIn('academic_year_id', $duplicateIds)->update(['academic_year_id' => $keepId]);
                    Schedule::whereIn('academic_year_id', $duplicateIds)->update(['academic_year_id' => $keepId]);
                    
                    // Delete duplicates
                    AcademicYear::whereIn('id', $duplicateIds)->delete();
                }
            }

            // Fix Semesters (Group by academic_year_id and semester_number)
            $semesters = Semester::all()->groupBy(function($item) {
                return $item->academic_year_id . '_' . $item->semester_number;
            });
            
            foreach ($semesters as $key => $group) {
                if ($group->count() > 1) {
                    $keepId = $group->first()->id;
                    $duplicateIds = $group->skip(1)->pluck('id')->toArray();
                    
                    $this->info("Ditemukan duplikat Semester {$key}. Menyimpan ID {$keepId} dan menghapus " . implode(',', $duplicateIds));
                    
                    // Update relations
                    TeachingAssignment::whereIn('semester_id', $duplicateIds)->update(['semester_id' => $keepId]);
                    Schedule::whereIn('semester_id', $duplicateIds)->update(['semester_id' => $keepId]);
                    
                    // Delete duplicates
                    Semester::whereIn('id', $duplicateIds)->delete();
                }
            }
            
            // Clean up Duplicate Schedules safely (Group by assignment, day, time_slot)
            // Just in case they got cloned multiple times
            $schedules = Schedule::all()->groupBy(function($item) {
                return $item->classroom_id . '_' . $item->day_of_week . '_' . $item->time_slot_id;
            });
            foreach ($schedules as $key => $group) {
                if ($group->count() > 1) {
                    $duplicateIds = $group->skip(1)->pluck('id')->toArray();
                    Schedule::whereIn('id', $duplicateIds)->delete();
                }
            }

            DB::commit();
            $this->info("Selesai membersihkan duplikat!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Gagal: " . $e->getMessage());
        }
    }
}
