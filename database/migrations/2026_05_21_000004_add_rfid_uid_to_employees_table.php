<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('rfid_uid')->nullable()->unique()->after('bank_account_name');
            $table->string('nip')->nullable()->after('employee_code');
            $table->string('nuptk')->nullable()->after('nip');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['rfid_uid', 'nip', 'nuptk']);
        });
    }
};
