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
        Schema::table('student_bills', function (Blueprint $table) {
            $table->boolean('late_fee_waived')->default(false)->after('status');
            $table->text('waiver_reason')->nullable()->after('late_fee_waived');
            $table->unsignedBigInteger('waived_by')->nullable()->after('waiver_reason');
            $table->timestamp('waived_at')->nullable()->after('waived_by');
            
            // Foreign key untuk user yang melakukan waiver
            $table->foreign('waived_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_bills', function (Blueprint $table) {
            $table->dropForeign(['waived_by']);
            $table->dropColumn(['late_fee_waived', 'waiver_reason', 'waived_by', 'waived_at']);
        });
    }
};
