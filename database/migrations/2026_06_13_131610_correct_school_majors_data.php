<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if schools exist to avoid foreign key failures during test runs on empty database
        $school2Exists = \Illuminate\Support\Facades\DB::table('schools')->where('id', 2)->exists();
        $school3Exists = \Illuminate\Support\Facades\DB::table('schools')->where('id', 3)->exists();

        if ($school2Exists) {
            // 1. Move IPA and IPS to school_id = 2 (SMA)
            \Illuminate\Support\Facades\DB::table('majors')
                ->whereIn('major_code', ['IPA', 'IPS'])
                ->update(['school_id' => 2]);
        }

        if ($school3Exists) {
            // 2. Move TSM to school_id = 3 (SMK)
            \Illuminate\Support\Facades\DB::table('majors')
                ->where('major_code', 'TSM')
                ->update(['school_id' => 3]);
        }

        // 3. Delete SD (SD Umum) if it belongs to school_id = 3
        \Illuminate\Support\Facades\DB::table('majors')
            ->where('major_code', 'SD')
            ->delete();

        // 4. Seed other standard SMK majors to make it complete
        if ($school3Exists) {
            $smkMajors = [
                ['major_code' => 'TJKT', 'major_name' => 'Teknik Jaringan Komputer & Telekomunikasi'],
                ['major_code' => 'TKR', 'major_name' => 'Teknik Kendaraan Ringan'],
                ['major_code' => 'TAV', 'major_name' => 'Teknik Audio Video'],
                ['major_code' => 'TE', 'major_name' => 'Teknik Elektronika'],
                ['major_code' => 'DPIB', 'major_name' => 'Desain Pemodelan & Informasi Bangunan'],
            ];

            foreach ($smkMajors as $major) {
                $exists = \Illuminate\Support\Facades\DB::table('majors')
                    ->where('school_id', 3)
                    ->where('major_code', $major['major_code'])
                    ->exists();
                if (!$exists) {
                    \Illuminate\Support\Facades\DB::table('majors')->insert([
                        'school_id' => 3,
                        'major_code' => $major['major_code'],
                        'major_name' => $major['major_name'],
                        'description' => 'Jurusan ' . $major['major_code'],
                        'is_active' => true,
                    ]);
                }
            }
        }
        // 5. Normalize classrooms table data:
        // A. Set program_keahlian_id & konsentrasi_keahlian_id to null for non-SMK schools (SMA & SMP)
        \Illuminate\Support\Facades\DB::table('classrooms')
            ->whereIn('school_id', function($query) {
                $query->select('id')->from('schools')
                      ->where('type', '!=', 'SMK')
                      ->where('type', '!=', 'SMKS');
            })
            ->where(function($query) {
                $query->whereNotNull('program_keahlian_id')
                      ->orWhereNotNull('konsentrasi_keahlian_id');
            })
            ->update([
                'program_keahlian_id' => null,
                'konsentrasi_keahlian_id' => null
            ]);

        // B. Set major_id to null for SMA grade X classrooms
        \Illuminate\Support\Facades\DB::table('classrooms')
            ->whereIn('school_id', function($query) {
                $query->select('id')->from('schools')
                      ->where('type', 'SMA')
                      ->orWhere('type', 'SMAS')
                      ->orWhere('type', 'LIKE', '%SMA%');
            })
            ->where('grade_level', 10)
            ->update(['major_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for data correction
    }
};
