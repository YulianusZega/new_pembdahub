<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_counseling_records', function (Blueprint $table) {
            $table->enum('category', ['akademik', 'perilaku', 'sosial', 'karir', 'pribadi', 'lainnya'])
                  ->default('lainnya')
                  ->after('record_type')
                  ->comment('Kategori spesifik (akademik/perilaku/dll)');
        });
    }

    public function down(): void
    {
        Schema::table('student_counseling_records', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
