<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module 3: Student Development Records
 *
 * Tables:
 * - student_counseling_records: Konseling, pembinaan, catatan kasus
 * - student_recommendations: Rekomendasi dari guru BK, wali kelas, PKS, kepsek
 * - student_development_notes: Catatan perkembangan umum
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Counseling / Pembinaan / Kasus records
        Schema::create('student_counseling_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');

            $table->enum('record_type', [
                'konseling',    // Sesi konseling
                'pembinaan',    // Pembinaan/coaching
                'pelanggaran',  // Pelanggaran/kasus
                'penghargaan',  // Penghargaan/prestasi (positive)
                'home_visit',   // Kunjungan rumah
            ])->comment('Jenis catatan');

            $table->enum('severity', ['ringan', 'sedang', 'berat'])->default('sedang')
                ->comment('Tingkat keparahan (for pelanggaran)');

            $table->string('title')->comment('Judul/ringkasan singkat');
            $table->text('description')->comment('Deskripsi lengkap kejadian/sesi');
            $table->text('background')->nullable()->comment('Latar belakang masalah');
            $table->text('action_taken')->nullable()->comment('Tindakan yang diambil');
            $table->text('result')->nullable()->comment('Hasil/respon siswa');
            $table->text('follow_up')->nullable()->comment('Rencana tindak lanjut');

            $table->date('incident_date')->comment('Tanggal kejadian/sesi');
            $table->string('location')->nullable()->comment('Tempat kejadian');

            $table->boolean('parent_notified')->default(false)->comment('Apakah orang tua sudah dihubungi');
            $table->date('parent_notified_date')->nullable();
            $table->text('parent_response')->nullable();

            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->date('resolved_date')->nullable();

            $table->foreignId('counselor_id')->comment('Guru BK/pencatat')
                ->constrained('users')->onDelete('cascade');
            $table->boolean('is_confidential')->default(false)->comment('Bersifat rahasia');

            $table->timestamps();

            $table->index(['student_id', 'record_type'], 'idx_scr_student_type');
            $table->index(['school_id', 'academic_year_id'], 'idx_scr_school_year');
            $table->index(['counselor_id', 'status'], 'idx_scr_counselor_status');
            $table->index('incident_date', 'idx_scr_incident_date');
        });

        // 2. Participants in counseling (additional teachers/staff involved)
        Schema::create('counseling_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counseling_record_id')
                ->constrained('student_counseling_records')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', [
                'guru_bk',        // Guru Bimbingan Konseling
                'wali_kelas',     // Wali Kelas
                'pks',            // Pembantu Kepala Sekolah
                'kepala_sekolah', // Kepala Sekolah
                'guru_mapel',     // Guru Mata Pelajaran
                'orang_tua',      // Orang Tua/Wali
                'lainnya',        // Pihak lain
            ])->comment('Peran dalam sesi');
            $table->text('notes')->nullable()->comment('Catatan dari partisipan');
            $table->timestamps();

            $table->unique(['counseling_record_id', 'user_id'], 'uq_cp_record_user');
        });

        // 3. Recommendations from staff
        Schema::create('student_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->foreignId('counseling_record_id')->nullable()
                ->constrained('student_counseling_records')->nullOnDelete()
                ->comment('Linked counseling record if applicable');

            $table->enum('recommender_role', [
                'guru_bk', 'wali_kelas', 'pks', 'kepala_sekolah', 'guru_mapel',
            ])->comment('Jabatan pemberi rekomendasi');

            $table->foreignId('recommended_by')->constrained('users')->onDelete('cascade');

            $table->enum('category', [
                'akademik',    // Rekomendasi akademik
                'perilaku',    // Rekomendasi perilaku
                'bakat',       // Bakat & minat
                'karir',       // Bimbingan karir
                'kesehatan',   // Kesehatan mental/fisik
                'sosial',      // Hubungan sosial
                'lainnya',
            ])->comment('Kategori rekomendasi');

            $table->string('title');
            $table->text('description')->comment('Isi rekomendasi');
            $table->text('expected_outcome')->nullable()->comment('Hasil yang diharapkan');
            $table->enum('priority', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'rejected'])
                ->default('pending');
            $table->text('action_result')->nullable()->comment('Hasil implementasi rekomendasi');
            $table->date('target_date')->nullable()->comment('Target penyelesaian');
            $table->timestamps();

            $table->index(['student_id', 'category'], 'idx_sr_student_category');
            $table->index(['school_id', 'semester_id'], 'idx_sr_school_semester');
            $table->index(['recommended_by', 'status'], 'idx_sr_recommender_status');
        });

        // 4. General development notes (catatan perkembangan)
        Schema::create('student_development_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');

            $table->enum('aspect', [
                'akademik',     // Perkembangan akademik
                'sikap',        // Sikap & perilaku
                'keterampilan', // Keterampilan (softskill/hardskill)
                'spiritual',    // Keagamaan/spiritual
                'sosial',       // Hubungan sosial
                'fisik',        // Perkembangan fisik/kesehatan
                'ekstrakurikuler', // Kegiatan ekskul
            ])->comment('Aspek perkembangan');

            $table->text('observation')->comment('Hasil observasi/pengamatan');
            $table->text('progress')->nullable()->comment('Kemajuan yang dicapai');
            $table->text('challenges')->nullable()->comment('Hambatan/tantangan');
            $table->text('suggestion')->nullable()->comment('Saran untuk siswa/orang tua');

            $table->foreignId('noted_by')->constrained('users')->onDelete('cascade');
            $table->enum('noted_by_role', [
                'guru_bk', 'wali_kelas', 'pks', 'kepala_sekolah', 'guru_mapel',
            ]);

            $table->timestamps();

            $table->index(['student_id', 'semester_id', 'aspect'], 'idx_sdn_student_sem_aspect');
            $table->index(['school_id', 'academic_year_id'], 'idx_sdn_school_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_development_notes');
        Schema::dropIfExists('student_recommendations');
        Schema::dropIfExists('counseling_participants');
        Schema::dropIfExists('student_counseling_records');
    }
};
