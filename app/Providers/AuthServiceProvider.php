<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Student;
use App\Models\Grade;
use App\Models\User;
use App\Policies\StudentPolicy;
use App\Policies\GradePolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Student::class => StudentPolicy::class,
        Grade::class => GradePolicy::class,
        User::class => UserPolicy::class,
        \App\Models\Employee::class => \App\Policies\EmployeePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define additional gates if needed
        Gate::define('manage-roles', function (User $user) {
            return $user->role === 'superadmin';
        });

        Gate::define('access-admin', function (User $user) {
            return in_array($user->role, ['superadmin', 'admin_sekolah']);
        });
    }
}
