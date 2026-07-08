<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Memperbaiki kode mata pelajaran yang salah (hasil fallback otomatis 4 huruf).
     * Kode yang salah seperti "PEND", "ILMU", "BIMB" diganti dengan singkatan resmi.
     */
    public function up(): void
    {
        // Fix by subject NAME untuk semua sekolah sekaligus
        $fixes = [
            // Nama Lengkap => Kode Resmi
            'Ilmu Pengetahuan Alam'           => 'IPA',
            'Ilmu Pengetahuan Sosial'          => 'IPS',
            'Pendidikan Kewarganegaraan'       => 'PKN',
            'Pendidikan Jasmani'               => 'PJOK',
            'Pendidikan Jasmani, Olahraga dan Kesehatan' => 'PJOK',
            'Pend. Agama Kristen'              => 'PAK',
            'Pend. Agama Katolik'              => 'PA-KAT',
            'Pend. Agama Islam'                => 'PAI',
            'Bimbingan Konseling'              => 'BK',
            'Seni Budaya'                      => 'SNB',
            'Prakarya'                         => 'PKRY',
            'Prakarya dan Kewirausahaan'       => 'PKRY',
            'Bahasa Daerah'                    => 'BDAE',
        ];

        foreach ($fixes as $name => $code) {
            DB::table('subjects')
                ->where('name', $name)
                ->update(['code' => $code]);
        }
    }

    /**
     * Kembalikan ke kode lama (fallback 4 huruf — hanya sebagai referensi rollback).
     * Catatan: rollback ini tidak 100% akurat karena kode asli berbeda-beda per id.
     */
    public function down(): void
    {
        $rollback = [
            'Ilmu Pengetahuan Alam'           => 'ILMU',
            'Ilmu Pengetahuan Sosial'          => 'ILMU',
            'Pendidikan Kewarganegaraan'       => 'PEND',
            'Pendidikan Jasmani'               => 'PEND',
            'Pendidikan Jasmani, Olahraga dan Kesehatan' => 'PJOK',
            'Pend. Agama Kristen'              => 'PEND',
            'Pend. Agama Katolik'              => 'PEND',
            'Pend. Agama Islam'                => 'PEND',
            'Bimbingan Konseling'              => 'BIMB',
            'Seni Budaya'                      => 'SBD',
            'Prakarya'                         => 'PRKR',
            'Prakarya dan Kewirausahaan'       => 'PKWU',
            'Bahasa Daerah'                    => 'BAHA',
        ];

        foreach ($rollback as $name => $code) {
            DB::table('subjects')
                ->where('name', $name)
                ->update(['code' => $code]);
        }
    }
};
