<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', [
                'ktp', 'npwp', 'kk', 'sk_pengangkatan', 'sk_jabatan',
                'ijazah', 'sertifikat', 'nuptk', 'kontrak', 'lainnya'
            ]);
            $table->string('document_name');
            $table->string('file_path');
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
