<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\PklPlacement;
use App\Models\PklLog;
use App\Models\PklGrade;
use App\Models\AlumniProfile;
use App\Models\TracerStudy;
use App\Models\JobPosting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PklAlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Menjalankan PklAlumniSeeder...');

        // 1. Get SMK school
        $smk = School::where('type', 'SMK')->first();
        if (!$smk) {
            $smk = School::create([
                'name' => 'SMK Swasta Pembda Nias',
                'type' => 'SMK',
                'npsn' => '20220003',
                'address' => 'Jl. Pelita No.31',
                'city' => 'Gunungsitoli',
                'province' => 'Sumatera Utara',
                'postal_code' => '22812',
                'phone' => '082168532567',
                'email' => 'info@smkpembda.sch.id',
                'principal_name' => 'Drs. Ama Zega',
                'school_year_start' => 2024,
                'is_active' => true,
            ]);
        }

        // 2. Get Academic Year
        $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        if (!$academicYear) {
            $academicYear = AcademicYear::create([
                'year' => '2024/2025',
                'start_date' => '2024-07-01',
                'end_date' => '2025-06-30',
                'semester_start' => '2024-07-01',
                'semester_end' => '2024-12-15',
                'is_active' => true,
            ]);
        }

        // 3. Find or Create Teacher
        $teacher = Teacher::where('school_id', $smk->id)->first();
        if (!$teacher) {
            $user = User::create([
                'name' => 'Bapak Pembimbing PKL',
                'email' => 'pembimbing.pkl@smkpembda.sch.id',
                'password' => Hash::make('Guru@2026!'),
                'role' => 'guru',
                'school_id' => $smk->id,
                'is_active' => true,
                'must_change_password' => false,
            ]);
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'school_id' => $smk->id,
                'nip' => '199001012020011001',
                'status' => 'aktif',
            ]);
        } else {
            // Ensure must_change_password is false to avoid redirect loops during testing
            $teacher->user->update(['must_change_password' => false]);
        }

        // 4. Find or Create Student (for active PKL)
        $student = Student::where('school_id', $smk->id)->where('status', 'aktif')->first();
        if (!$student) {
            $user = User::create([
                'name' => 'Budianto Harefa',
                'email' => 'budi@student.sch.id',
                'password' => Hash::make('Siswa@2026!'),
                'role' => 'siswa',
                'school_id' => $smk->id,
                'is_active' => true,
                'must_change_password' => false,
            ]);
            $student = Student::create([
                'user_id' => $user->id,
                'school_id' => $smk->id,
                'nisn' => '0098765432',
                'nis' => '2024001',
                'full_name' => $user->name,
                'gender' => 'L',
                'birth_place' => 'Gunungsitoli',
                'birth_date' => '2008-05-15',
                'religion' => 'Kristen',
                'address' => 'Jl. Pelita No.31, Gunungsitoli',
                'phone' => '081234567890',
                'parent_name' => 'Orang Tua Budi',
                'parent_phone' => '081234567891',
                'entry_year' => 2024,
                'status' => 'aktif',
            ]);
        } else {
            // Ensure must_change_password is false
            $student->user->update(['must_change_password' => false]);
        }

        // 5. Create Placement
        $placement = PklPlacement::where('student_id', $student->id)->where('status', 'active')->first();
        if (!$placement) {
            $placement = PklPlacement::create([
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
                'company_name' => 'PT. Telekomunikasi Indonesia (Telkom) Witel Nias',
                'company_address' => 'Jl. Pancasila No. 12, Gunungsitoli, Nias',
                'mentor_name' => 'Albert Halawa',
                'mentor_phone' => '081299887766',
                'start_date' => now()->subMonths(1)->format('Y-m-d'),
                'end_date' => now()->addMonths(2)->format('Y-m-d'),
                'teacher_id' => $teacher->id,
                'status' => 'active',
                'signed_token' => Str::random(32),
            ]);
        }

        // 6. Seed Daily Logs
        if ($placement->logs()->count() === 0) {
            PklLog::create([
                'pkl_placement_id' => $placement->id,
                'log_date' => now()->subDays(3)->format('Y-m-d'),
                'activity' => 'Melakukan penarikan dan instalasi kabel fiber optik (drop core) sepanjang 100 meter untuk pelanggan baru Indihome.',
                'photo' => null,
                'latitude' => 1.282928,
                'longitude' => 97.619028,
                'status' => 'approved',
                'approved_at' => now()->subDays(2),
            ]);

            PklLog::create([
                'pkl_placement_id' => $placement->id,
                'log_date' => now()->subDays(2)->format('Y-m-d'),
                'activity' => 'Mempelajari konfigurasi ONT Huawei dan melalukan penyetingan modem wifi pelanggan secara langsung di lapangan.',
                'photo' => null,
                'latitude' => 1.283000,
                'longitude' => 97.619100,
                'status' => 'approved',
                'approved_at' => now()->subDays(1),
            ]);

            PklLog::create([
                'pkl_placement_id' => $placement->id,
                'log_date' => now()->subDays(1)->format('Y-m-d'),
                'activity' => 'Membantu admin DUDI merapikan data inventori perangkat modem (ONT & STB) yang masuk ke gudang Telkom.',
                'photo' => null,
                'latitude' => 1.281500,
                'longitude' => 97.620000,
                'status' => 'submitted',
            ]);
        }

        // 7. Find or Create Alumni Profile
        $alumniStudent = Student::where('school_id', $smk->id)->where('status', 'lulus')->first();
        if (!$alumniStudent) {
            $user = User::create([
                'name' => 'Sartika Mendrofa',
                'email' => 'sartika@alumni.sch.id',
                'password' => Hash::make('Siswa@2026!'),
                'role' => 'siswa',
                'school_id' => $smk->id,
                'is_active' => true,
                'must_change_password' => false,
            ]);
            $alumniStudent = Student::create([
                'user_id' => $user->id,
                'school_id' => $smk->id,
                'nisn' => '0076543210',
                'nis' => '2021001',
                'full_name' => $user->name,
                'gender' => 'P',
                'birth_place' => 'Gunungsitoli',
                'birth_date' => '2005-09-20',
                'religion' => 'Kristen',
                'address' => 'Jl. Pelita No.31, Gunungsitoli',
                'phone' => '082234567890',
                'parent_name' => 'Orang Tua Sartika',
                'parent_phone' => '082234567891',
                'entry_year' => 2021,
                'status' => 'lulus',
            ]);
        } else {
            $alumniStudent->user->update(['must_change_password' => false]);
        }

        $alumniProfile = AlumniProfile::where('student_id', $alumniStudent->id)->first();
        if (!$alumniProfile) {
            $alumniProfile = AlumniProfile::create([
                'student_id' => $alumniStudent->id,
                'school_id' => $smk->id,
                'full_name' => $alumniStudent->full_name,
                'graduation_year' => 2024,
                'phone' => $alumniStudent->phone,
                'email' => $alumniStudent->user->email,
            ]);
        }

        // 8. Seed Tracer Study response
        if (TracerStudy::where('alumni_profile_id', $alumniProfile->id)->count() === 0) {
            TracerStudy::create([
                'alumni_profile_id' => $alumniProfile->id,
                'employment_status' => 'kerja',
                'company_name' => 'PT. Bank Mandiri (Persero) Tbk Area Nias',
                'job_title' => 'Customer Service Representative',
                'salary_range' => 'Rp 3.000.000 - Rp 5.000.000',
                'survey_date' => now()->subDays(5),
                'feedback_for_school' => 'Sangat menyarankan sekolah memperbanyak praktik bahasa Inggris bisnis dan penggunaan spreadsheet/Excel lanjutan untuk urusan administratif.',
            ]);
        }

        // 9. Seed Job Postings
        $adminUser = User::where('role', 'superadmin')->first() ?? User::first();
        if ($adminUser && JobPosting::count() === 0) {
            JobPosting::create([
                'company_name' => 'CV. Gunungsitoli Solusi Teknologi',
                'title' => 'Junior Web Developer & Network Technician',
                'description' => 'Membantu instalasi jaringan kabel/nirkabel klien, serta melakukan maintenance website internal berbasis PHP dan WordPress.',
                'requirements' => "- Lulusan SMKS Swasta Pembda Nias Jurusan TJKT\n- Menguasai dasar networking (Mikrotik, Switch)\n- Mengenal dasar PHP & Database MySQL\n- Jujur, pekerja keras, dan bertanggung jawab",
                'contact_email' => 'hrd@gstolisolusi.co.id',
                'contact_phone' => '081269002211',
                'salary_range' => 'Rp 2.000.000 - Rp 3.000.000',
                'is_active' => true,
                'created_by' => $adminUser->id,
            ]);

            JobPosting::create([
                'company_name' => 'Bengkel Motor Pembda Motor',
                'title' => 'Mekanik Sepeda Motor (Asisten)',
                'description' => 'Melakukan tune-up, servis berkala, ganti oli, dan perbaikan ringan kendaraan roda dua pelanggan.',
                'requirements' => "- Lulusan SMKS Swasta Pembda Nias Jurusan TSM\n- Memiliki pengetahuan baik mengenai mesin 4-tak\n- Memiliki peralatan kerja dasar sendiri merupakan nilai tambah\n- Mampu bekerjasama dalam tim",
                'contact_email' => 'pembdamotor@gmail.com',
                'contact_phone' => '085270003000',
                'salary_range' => 'Rp 1.500.000 - Rp 2.500.000',
                'is_active' => true,
                'created_by' => $adminUser->id,
            ]);
        }

        $this->command->info('✅ PklAlumniSeeder selesai!');
    }
}
