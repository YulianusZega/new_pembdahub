<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds a JSON column to store custom document types per school.
     * Format: [{"key": "surat_keterangan", "label": "Surat Keterangan Sehat"}, ...]
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->json('psb_custom_document_types')->nullable()->after('psb_required_documents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('psb_custom_document_types');
        });
    }
};
