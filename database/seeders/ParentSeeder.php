<?php

namespace Database\Seeders;

use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::limit(20)->get();

        foreach ($students as $s) {
            // Create a user for parent
            $email = 'parent+' . $s->nisn . '@parents.local';
            if (User::where('email', $email)->exists()) {
                continue;
            }

            $user = User::create([
                'name' => 'Orangtua ' . $s->full_name,
                'email' => $email,
                'password' => Hash::make('OrangTua@2026!'),
                'role' => 'orang_tua',
                'school_id' => $s->school_id,
                'is_active' => true,
                'must_change_password' => true,
            ]);

            ParentModel::create([
                'user_id' => $user->id,
                'student_id' => $s->id,
                'relation_type' => 'ibu',
                'full_name' => 'Ibu ' . explode(' ', $s->full_name)[0],
                'phone' => '0812' . rand(10000000, 99999999),
                'email' => $email,
            ]);
        }

        $this->command->info('Parents seeded successfully!');
    }
}
