<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Services\EmployeeAssignmentService;
use Illuminate\Support\Facades\Log;

class EmployeeObserver
{
    public function __construct(private EmployeeAssignmentService $service) {}

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        $watchedFields = [
            'basic_salary',
            'marital_status', 
            'children_count', 
            'employment_status', 
            'school_id',
            'is_active'
        ];

        if ($employee->isDirty($watchedFields)) {
            $year = AcademicYear::where('is_active', true)->first();
            $semester = Semester::where('is_active', true)->first();

            if ($year && $semester) {
                try {
                    $this->service->calculateWorkload($employee, $year, $semester);
                } catch (\Exception $e) {
                    Log::error("Failed to auto-recalculate workload for employee {$employee->full_name}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Handle the Employee "created" event.
     * Optional: Automatically create a draft summary for the current active semester.
     */
    public function created(Employee $employee): void
    {
        $year = AcademicYear::where('is_active', true)->first();
        $semester = Semester::where('is_active', true)->first();

        if ($year && $semester && $employee->is_active) {
            try {
                $this->service->calculateWorkload($employee, $year, $semester);
            } catch (\Exception $e) {
                Log::error("Failed to auto-create workload for new employee {$employee->full_name}: " . $e->getMessage());
            }
        }
    }
}
