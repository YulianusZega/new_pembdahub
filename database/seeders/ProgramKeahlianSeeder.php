<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\ProgramKeahlian;
use App\Models\KonsentrasiKeahlian;

class ProgramKeahlianSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh: SMK Negeri 1
        $smk = School::where('type', 'SMK')->first();
        if (!$smk) return;

        $tkj = ProgramKeahlian::create([
            'school_id' => $smk->id,
            'kode' => 'TKJ',
            'nama' => 'Teknik Komputer dan Jaringan',
            'deskripsi' => 'Program Keahlian TKJ',
            'is_active' => true,
        ]);
        KonsentrasiKeahlian::create([
            'program_keahlian_id' => $tkj->id,
            'kode' => 'TKJ-1',
            'nama' => 'Jaringan Komputer',
            'deskripsi' => 'Konsentrasi Jaringan',
            'is_active' => true,
        ]);
        KonsentrasiKeahlian::create([
            'program_keahlian_id' => $tkj->id,
            'kode' => 'TKJ-2',
            'nama' => 'Teknisi Komputer',
            'deskripsi' => 'Konsentrasi Teknisi',
            'is_active' => true,
        ]);

        $rpl = ProgramKeahlian::create([
            'school_id' => $smk->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Program Keahlian RPL',
            'is_active' => true,
        ]);
        KonsentrasiKeahlian::create([
            'program_keahlian_id' => $rpl->id,
            'kode' => 'RPL-1',
            'nama' => 'Pengembangan Web',
            'deskripsi' => 'Konsentrasi Web',
            'is_active' => true,
        ]);
    }
}
