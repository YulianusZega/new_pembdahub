<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\School;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all schools
        $schools = School::all();

        // SMA/SMK Subjects
        $smaSubjects = [
            ['code' => 'MTK', 'name' => 'Matematika', 'description' => 'Ilmu pengetahuan tentang angka dan ruang'],
            ['code' => 'BIN', 'name' => 'Bahasa Indonesia', 'description' => 'Bahasa nasional Indonesia'],
            ['code' => 'ENG', 'name' => 'Bahasa Inggris', 'description' => 'Bahasa internasional'],
            ['code' => 'FIS', 'name' => 'Fisika', 'description' => 'Ilmu pengetahuan alam tentang energi dan gerak'],
            ['code' => 'KIM', 'name' => 'Kimia', 'description' => 'Ilmu pengetahuan tentang zat dan reaksi'],
            ['code' => 'BIO', 'name' => 'Biologi', 'description' => 'Ilmu pengetahuan tentang makhluk hidup'],
            ['code' => 'SEJARAH', 'name' => 'Sejarah', 'description' => 'Mempelajari peristiwa masa lalu'],
            ['code' => 'GEO', 'name' => 'Geografi', 'description' => 'Ilmu pengetahuan tentang bumi dan penduduk'],
            ['code' => 'EKONOMI', 'name' => 'Ekonomi', 'description' => 'Ilmu pengetahuan tentang ekonomi'],
            ['code' => 'SOC', 'name' => 'Sosiologi', 'description' => 'Ilmu pengetahuan tentang masyarakat'],
            ['code' => 'PENJASKES', 'name' => 'Pendidikan Jasmani', 'description' => 'Olahraga dan kesehatan'],
            ['code' => 'SENI', 'name' => 'Seni Budaya', 'description' => 'Seni dan budaya lokal'],
        ];

        // SMP Subjects
        $smpSubjects = [
            ['code' => 'MTK', 'name' => 'Matematika', 'description' => 'Ilmu pengetahuan tentang angka'],
            ['code' => 'BIN', 'name' => 'Bahasa Indonesia', 'description' => 'Bahasa nasional'],
            ['code' => 'ENG', 'name' => 'Bahasa Inggris', 'description' => 'Bahasa internasional'],
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam', 'description' => 'Fisika, Kimia, dan Biologi'],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial', 'description' => 'Sejarah, Geografi, Ekonomi'],
            ['code' => 'PKN', 'name' => 'Pendidikan Kewarganegaraan', 'description' => 'Pelajaran tentang negara'],
            ['code' => 'PENJASKES', 'name' => 'Pendidikan Jasmani', 'description' => 'Olahraga dan kesehatan'],
            ['code' => 'SENI', 'name' => 'Seni Budaya', 'description' => 'Seni dan budaya'],
        ];

        // SD Subjects
        $sdSubjects = [
            ['code' => 'MTK', 'name' => 'Matematika', 'description' => 'Berhitung'],
            ['code' => 'BIN', 'name' => 'Bahasa Indonesia', 'description' => 'Membaca dan menulis'],
            ['code' => 'ENG', 'name' => 'Bahasa Inggris', 'description' => 'Bahasa asing pertama'],
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam', 'description' => 'Sains dasar'],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial', 'description' => 'Pengetahuan sosial'],
            ['code' => 'PENJASKES', 'name' => 'Pendidikan Jasmani', 'description' => 'Olahraga'],
            ['code' => 'SENI', 'name' => 'Seni Budaya', 'description' => 'Seni'],
        ];

        // SMK Subjects (Umum + Kejuruan)
        $smkSubjects = [
            ['code' => 'MTK', 'name' => 'Matematika', 'description' => 'Matematika umum dan terapan untuk SMK'],
            ['code' => 'BIN', 'name' => 'Bahasa Indonesia', 'description' => 'Bahasa dan sastra Indonesia'],
            ['code' => 'ENG', 'name' => 'Bahasa Inggris', 'description' => 'Bahasa Inggris untuk dunia kerja'],
            ['code' => 'PKN', 'name' => 'Pendidikan Kewarganegaraan', 'description' => 'Kewarganegaraan'],
            ['code' => 'PENJASKES', 'name' => 'Pendidikan Jasmani', 'description' => 'Olahraga dan kesehatan'],
            ['code' => 'SENI', 'name' => 'Seni Budaya', 'description' => 'Seni dan budaya'],
            ['code' => 'PEMWEB', 'name' => 'Pemrograman Web', 'description' => 'Pembuatan website dengan HTML, CSS, JavaScript, dan PHP'],
            ['code' => 'JARKOM', 'name' => 'Jaringan Komputer', 'description' => 'Instalasi dan konfigurasi jaringan komputer'],
            ['code' => 'BASDAT', 'name' => 'Basis Data', 'description' => 'Perancangan dan pengelolaan database'],
            ['code' => 'SISOP', 'name' => 'Sistem Operasi', 'description' => 'Instalasi dan konfigurasi sistem operasi'],
            ['code' => 'AKDAS', 'name' => 'Akuntansi Dasar', 'description' => 'Prinsip-prinsip dasar akuntansi'],
            ['code' => 'PAKUN', 'name' => 'Praktikum Akuntansi', 'description' => 'Praktek pembukuan dan laporan keuangan'],
            ['code' => 'EKBIS', 'name' => 'Ekonomi Bisnis', 'description' => 'Konsep ekonomi dan bisnis'],
        ];

        foreach ($schools as $school) {
            if ($school->type === 'SMA') {
                foreach ($smaSubjects as $subject) {
                    \DB::table('subjects')->insert([
                        'school_id' => $school->id,
                        'subject_code' => $subject['code'],
                        'subject_name' => $subject['name'],
                        'description' => $subject['description'],
                        'is_active' => true,
                        'kkm' => 75,
                    ]);
                }
            } elseif ($school->type === 'SMP') {
                foreach ($smpSubjects as $subject) {
                    \DB::table('subjects')->insert([
                        'school_id' => $school->id,
                        'subject_code' => $subject['code'],
                        'subject_name' => $subject['name'],
                        'description' => $subject['description'],
                        'is_active' => true,
                        'kkm' => 75,
                    ]);
                }
            } elseif ($school->type === 'SD') {
                foreach ($sdSubjects as $subject) {
                    \DB::table('subjects')->insert([
                        'school_id' => $school->id,
                        'subject_code' => $subject['code'],
                        'subject_name' => $subject['name'],
                        'description' => $subject['description'],
                        'is_active' => true,
                        'kkm' => 75,
                    ]);
                }
            } elseif ($school->type === 'SMK') {
                foreach ($smkSubjects as $subject) {
                    \DB::table('subjects')->insert([
                        'school_id' => $school->id,
                        'subject_code' => $subject['code'],
                        'subject_name' => $subject['name'],
                        'description' => $subject['description'],
                        'is_active' => true,
                        'kkm' => 75,
                    ]);
                }
            }
        }

        $this->command->info('Subjects seeded successfully!');
    }
}
