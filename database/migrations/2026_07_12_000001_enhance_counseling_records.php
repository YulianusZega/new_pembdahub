<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_counseling_records', function (Blueprint $table) {
            // Field baru untuk Prestasi
            $table->string('competition_name')->nullable()->after('achievement_level')
                ->comment('Nama lomba/kompetisi');
            $table->string('organizer')->nullable()->after('competition_name')
                ->comment('Penyelenggara lomba/kompetisi');
            $table->string('ranking')->nullable()->after('organizer')
                ->comment('Peringkat: juara_1, juara_2, juara_3, harapan_1, harapan_2, harapan_3, finalis, peserta');

            // Field baru untuk Pembinaan
            $table->text('sanction')->nullable()->after('action_taken')
                ->comment('Sanksi yang diberikan');
            $table->string('sanction_type')->nullable()->after('sanction')
                ->comment('Jenis sanksi: teguran_lisan, surat_peringatan, skorsing, hukuman_akademik, hukuman_sosial, pengembalian_ortu, pemindahan, lainnya');
            $table->integer('sanction_duration_days')->nullable()->after('sanction_type')
                ->comment('Durasi sanksi dalam hari (untuk skorsing)');
        });
    }

    public function down(): void
    {
        Schema::table('student_counseling_records', function (Blueprint $table) {
            $table->dropColumn([
                'competition_name', 'organizer', 'ranking',
                'sanction', 'sanction_type', 'sanction_duration_days'
            ]);
        });
    }
};
