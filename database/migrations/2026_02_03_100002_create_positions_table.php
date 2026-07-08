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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade')
                ->comment('NULL = berlaku untuk semua sekolah (position global)');
            $table->string('position_name', 100)->comment('Nama jabatan');
            $table->string('position_code', 20)->nullable()->comment('Kode jabatan');
            
            $table->enum('position_category', [
                'structural',      // Jabatan struktural: Kepala Sekolah, Wakil Kepala
                'functional',      // Jabatan fungsional: Guru, Wali Kelas
                'staff',          // Staff: TU, Keuangan, Perpustakaan
                'support'         // Support: Security, Cleaning
            ])->default('functional');
            
            $table->integer('position_level')->default(3)->comment('1=top, 2=middle, 3=staff');
            $table->boolean('is_structural')->default(false)->comment('Dapat tunjangan struktural');
            $table->decimal('allowance_amount', 15, 2)->default(0)->comment('Tunjangan jabatan');
            
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('school_id');
            $table->index('position_category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
