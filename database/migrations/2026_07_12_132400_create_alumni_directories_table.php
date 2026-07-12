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
        Schema::create('alumni_directories', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('occupation')->nullable();
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
            $table->integer('graduation_year')->nullable();
            $table->string('last_class')->nullable();
            $table->text('message')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_directories');
    }
};
