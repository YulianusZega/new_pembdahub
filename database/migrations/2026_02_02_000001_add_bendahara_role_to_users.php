<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum to add 'bendahara' role
        if (DB::getDriverName() === 'sqlite') {
            // SQLite stores enum as CHECK constraint — recreate the table column
            // Drop the CHECK constraint by recreating the column via pragma
            DB::statement("PRAGMA writable_schema = ON");
            $sql = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='users'");
            if ($sql) {
                $originalSql = $sql->sql;
                $newSql = str_replace(
                    "\"role\" varchar check (\"role\" in ('superadmin', 'admin_sekolah', 'guru', 'siswa', 'orang_tua'))",
                    "\"role\" varchar check (\"role\" in ('superadmin', 'admin_sekolah', 'bendahara', 'guru', 'siswa', 'orang_tua'))",
                    $originalSql
                );
                if ($newSql !== $originalSql) {
                    DB::statement("UPDATE sqlite_master SET sql = ? WHERE type='table' AND name='users'", [$newSql]);
                }
            }
            DB::statement("PRAGMA writable_schema = OFF");
            DB::statement("PRAGMA integrity_check");
            return;
        }
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin_sekolah', 'bendahara', 'guru', 'siswa', 'orang_tua')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        // Remove 'bendahara' role from enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin_sekolah', 'guru', 'siswa', 'orang_tua')");
    }
};
