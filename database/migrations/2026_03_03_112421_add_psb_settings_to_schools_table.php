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
        Schema::table('schools', function (Blueprint $table) {
            $table->json('psb_required_documents')->nullable();
            $table->string('psb_contact_person')->nullable();
            $table->string('psb_contact_phone')->nullable();
            $table->text('psb_opening_hours')->nullable();
            $table->text('psb_secretariat')->nullable();
            $table->longText('psb_description')->nullable();
            $table->boolean('psb_is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'psb_required_documents',
                'psb_contact_person',
                'psb_contact_phone',
                'psb_opening_hours',
                'psb_secretariat',
                'psb_description',
                'psb_is_active'
            ]);
        });
    }
};
