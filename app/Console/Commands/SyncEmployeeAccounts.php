<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SyncEmployeeAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:sync-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user accounts for employees who do not have one yet based on the new rules.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting employee account synchronization...');

        $employees = Employee::whereNull('user_id')->get();
        
        if ($employees->isEmpty()) {
            $this->info('No employees found without an account. All good!');
            return 0;
        }

        $count = 0;

        foreach ($employees as $employee) {
            $firstName = strtolower(explode(' ', trim($employee->full_name))[0]);
            $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);
            
            if (empty($firstName)) {
                $firstName = 'pegawai' . rand(100, 999);
            }

            $baseEmail = $firstName . '@pembdahub.com';
            $email = $baseEmail;
            $username = $firstName;
            $counter = 1;

            while (User::where('email', $email)->orWhere('username', $username)->exists()) {
                $email = $firstName . $counter . '@pembdahub.com';
                $username = $firstName . $counter;
                $counter++;
            }

            $role = 'pegawai';
            if ($employee->employee_type === 'guru') {
                $role = 'guru';
            }

            $user = User::create([
                'name' => $employee->full_name,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make('pembdahub2026'),
                'role' => $role,
                'school_id' => $employee->school_id,
                'is_active' => $employee->is_active,
            ]);

            $employee->update(['user_id' => $user->id]);
            
            $this->line("Created account for {$employee->full_name}: {$email}");
            $count++;
        }

        $this->info("Successfully synchronized $count employee accounts.");
        return 0;
    }
}
