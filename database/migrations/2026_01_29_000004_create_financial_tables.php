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
        // ================================================================
        // 4. KEUANGAN & PEMBAYARAN
        // ================================================================

        // Tabel Jenis Pembayaran
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('type_code', 20)->comment('SPP, SERAGAM, DAFTAR, ALAT, DUKU, NATAL, KELAS, OSIS');
            $table->string('type_name', 100);
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2)->comment('Nominal standar (bisa berbeda per siswa)');
            $table->boolean('is_recurring')->default(false)->comment('TRUE untuk SPP (bulanan)');
            $table->boolean('allow_installment')->default(true)->comment('TRUE = boleh cicil (sesuai BR: semua boleh cicil)');
            $table->boolean('is_active')->default(true);

            $table->index('school_id');
            $table->index('type_code');
        });

        // Tabel Tagihan Siswa
        Schema::create('student_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('payment_type_id')->constrained('payment_types')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('set null');
            $table->tinyInteger('month')->nullable()->comment('1-12 untuk SPP bulanan, NULL untuk non-recurring');
            $table->year('year')->nullable()->comment('Tahun tagihan (untuk SPP)');
            $table->decimal('amount', 12, 2)->comment('Total tagihan');
            $table->decimal('paid_amount', 12, 2)->default(0)->comment('Total yang sudah dibayar (support cicilan)');
            $table->date('due_date')->nullable()->comment('Jatuh tempo (SPP: tgl 10 setiap bulan)');
            $table->enum('status', ['belum_bayar', 'cicilan', 'lunas'])->default('belum_bayar');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('student_id');
            $table->index('status');
            $table->index('due_date');
            $table->index(['month', 'year']);
        });

        // Tabel Pembayaran
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('student_bills')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('payment_method', ['cash', 'transfer', 'qris']);
            $table->decimal('amount_paid', 12, 2)->comment('Nominal yang dibayar (bisa cicilan)');
            $table->dateTime('payment_date');
            $table->string('reference_number', 100)->nullable()->comment('No. referensi QRIS/Transfer');
            $table->string('qris_transaction_id', 100)->nullable()->comment('Transaction ID dari Midtrans');
            $table->enum('qris_status', ['pending', 'success', 'failed', 'expired'])->nullable();
            $table->string('proof_file', 255)->nullable()->comment('Bukti transfer (jika method=transfer)');
            $table->text('notes')->nullable();
            $table->string('receipt_number', 50)->unique()->nullable()->comment('Nomor kwitansi (auto-generate)');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_verified')->default(false)->comment('TRUE jika sudah diverifikasi (untuk transfer manual)');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('verified_at')->nullable()->comment('Waktu verifikasi');
            $table->timestamps();

            $table->index('student_id');
            $table->index('payment_date');
            $table->index('payment_method');
            $table->index('qris_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('student_bills');
        Schema::dropIfExists('payment_types');
    }
};
