<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Dinonaktifkan: migrasi ini tidak kompatibel dengan struktur awal tabel attendances hasil migrate:fresh
    }

    public function down(): void
    {
        // Dinonaktifkan: migrasi ini tidak kompatibel dengan struktur awal tabel attendances hasil migrate:fresh
    }
};
