<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GradePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua role bisa melihat nilai (dengan filter di controller)
        return in_array($user->role, ['superadmin', 'admin_sekolah', 'kepala_sekolah', 'guru', 'siswa', 'orang_tua']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Grade $grade): bool
    {
        // SuperAdmin, Admin Sekolah, dan Kepala Sekolah bisa melihat semua nilai
        if (in_array($user->role, ['superadmin', 'admin_sekolah', 'kepala_sekolah'])) {
            return true;
        }

        // Guru bisa melihat nilai yang dia input
        if ($user->role === 'guru') {
            return $user->teacher && $grade->teacher_id === $user->teacher->id;
        }

        // Siswa hanya bisa melihat nilainya sendiri
        if ($user->role === 'siswa') {
            $student = $user->student;
            return $student && $grade->student_id === $student->id;
        }

        // Orang tua bisa melihat nilai anaknya
        if ($user->role === 'orang_tua') {
            return $grade->student->parents()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // SuperAdmin, Admin Sekolah, dan Guru bisa menambah nilai
        return in_array($user->role, ['superadmin', 'admin_sekolah', 'guru']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Grade $grade): bool
    {
        // SuperAdmin dan Admin Sekolah bisa update semua nilai
        if (in_array($user->role, ['superadmin', 'admin_sekolah'])) {
            return true;
        }

        // Guru hanya bisa update nilai yang dia input sendiri
        if ($user->role === 'guru') {
            return $user->teacher && $grade->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Grade $grade): bool
    {
        // SuperAdmin dan Admin Sekolah bisa hapus semua nilai
        if (in_array($user->role, ['superadmin', 'admin_sekolah'])) {
            return true;
        }

        // Guru hanya bisa hapus nilai yang dia input sendiri
        if ($user->role === 'guru') {
            return $user->teacher && $grade->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Grade $grade): bool
    {
        return in_array($user->role, ['superadmin', 'admin_sekolah']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Grade $grade): bool
    {
        return $user->role === 'superadmin';
    }

    /**
     * Determine whether the user can perform bulk grade operations.
     */
    public function bulkCreate(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin_sekolah', 'guru']);
    }
}
