<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;

class EmployeePolicy
{
    /**
     * Determine if the user can view any employees.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdminSekolah() || $user->isKepalaSekolah();
    }

    /**
     * Determine if the user can view the employee.
     */
    public function view(User $user, Employee $employee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admin sekolah dan Kepala Sekolah can only view employees from their school
        return ($user->isAdminSekolah() || $user->isKepalaSekolah()) && $user->school_id === $employee->school_id;
    }

    /**
     * Determine if the user can create employees.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdminSekolah();
    }

    /**
     * Determine if the user can update the employee.
     */
    public function update(User $user, Employee $employee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admin sekolah can only update employees from their school
        return $user->isAdminSekolah() && $user->school_id === $employee->school_id;
    }

    /**
     * Determine if the user can delete the employee.
     */
    public function delete(User $user, Employee $employee): bool
    {
        // Prevent deleting teachers through employee interface
        if ($employee->employee_type === 'guru') {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admin sekolah can only delete employees from their school
        return $user->isAdminSekolah() && $user->school_id === $employee->school_id;
    }
}
