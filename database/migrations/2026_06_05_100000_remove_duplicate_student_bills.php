<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Find duplicate bill groups (same student, same payment type, same academic year, same month, same year)
        $duplicates = DB::table('student_bills')
            ->select('student_id', 'payment_type_id', 'academic_year_id', 'month', 'year')
            ->groupBy('student_id', 'payment_type_id', 'academic_year_id', 'month', 'year')
            ->having(DB::raw('count(*)'), '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            // Get all bills in this duplicate group
            $bills = DB::table('student_bills')
                ->where('student_id', $dup->student_id)
                ->where('payment_type_id', $dup->payment_type_id)
                ->where('academic_year_id', $dup->academic_year_id)
                ->where('month', $dup->month)
                ->where('year', $dup->year)
                ->orderByDesc('paid_amount') // Keep the one with payment first
                ->orderBy('id')
                ->get();

            if ($bills->isEmpty()) {
                continue;
            }

            // The first one is the one we want to keep
            $keepId = $bills->first()->id;

            // Delete the rest, but ONLY if they are unpaid (paid_amount == 0 and has no payments records)
            foreach ($bills->slice(1) as $billToDelete) {
                $hasPayments = DB::table('payments')->where('bill_id', $billToDelete->id)->exists();
                if (!$hasPayments && $billToDelete->paid_amount == 0) {
                    DB::table('student_bills')->where('id', $billToDelete->id)->delete();
                }
            }
        }
    }

    public function down(): void
    {
        // Destruction clean up, no rollback possible
    }
};
