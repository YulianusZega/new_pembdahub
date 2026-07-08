<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_counseling_records', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('is_confidential')->comment('File path attachment (surat/bukti/piagam)');
            $table->string('attachment_name')->nullable()->after('attachment')->comment('Original filename');
        });
    }

    public function down(): void
    {
        Schema::table('student_counseling_records', function (Blueprint $table) {
            $table->dropColumn(['attachment', 'attachment_name']);
        });
    }
};
