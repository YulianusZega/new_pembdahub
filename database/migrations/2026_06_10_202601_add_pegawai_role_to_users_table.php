<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'pegawai' to the enum list
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin_sekolah', 'bendahara', 'ketua_yayasan', 'guru', 'siswa', 'orang_tua', 'pegawai') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum list
        // Note: This might fail if there are records with 'pegawai' role, so normally you'd handle that first.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin_sekolah', 'bendahara', 'ketua_yayasan', 'guru', 'siswa', 'orang_tua') NOT NULL");
        }
    }
};
