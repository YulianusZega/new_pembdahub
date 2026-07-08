<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Repositories\StudentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StudentService
{
    public function __construct(
        private StudentRepository $studentRepository
    ) {}

    /**
     * Create student with user account
     */
    public function createStudentWithUser(array $data, ?UploadedFile $photo = null): Student
    {
        return DB::transaction(function () use ($data, $photo) {
            // Generate unique email
            $email = !empty($data['email']) ? $data['email'] : ($data['nisn'] . '@students.local');
            $email = $this->generateUniqueEmail($email);

            // Generate unique username
            $username = $this->generateUniqueUsername($data['nisn']);

            // Create user account
            $user = User::create([
                'name' => $data['full_name'],
                'username' => $username,
                'email' => $email,
                'password' => Hash::make(Str::random(12)),
                'role' => 'siswa',
                'school_id' => $data['school_id'],
                'is_active' => true,
            ]);

            // Handle photo upload
            if ($photo) {
                $data['photo'] = $photo->store('photos/students', 'public');
            }

            // Create student
            $data['user_id'] = $user->id;
            $data['status'] = 'aktif';

            return $this->studentRepository->create($data);
        });
    }

    /**
     * Update student data
     */
    public function updateStudent(Student $student, array $data, ?UploadedFile $photo = null, bool $removePhoto = false): bool
    {
        // Handle photo removal
        if ($removePhoto && $student->photo) {
            Storage::disk('public')->delete($student->photo);
            $data['photo'] = null;
        }

        // Handle photo replacement
        if ($photo) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $photo->store('photos/students', 'public');
        }

        return $this->studentRepository->update($student, $data);
    }

    /**
     * Delete student and associated user
     */
    public function deleteStudent(Student $student): bool
    {
        return DB::transaction(function () use ($student) {
            // Delete photo if exists
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }

            // Store user for deletion
            $user = $student->user;

            // Delete student
            $deleted = $this->studentRepository->delete($student);

            // Delete associated user if exists
            if ($deleted && $user) {
                $user->delete();
            }

            return $deleted;
        });
    }

    /**
     * Import students from CSV data
     */
    public function importStudents(array $rows): array
    {
        $imported = 0;
        $failed = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                try {
                    // Validate required fields
                    if (empty($row['nisn']) || empty($row['full_name'])) {
                        $failed++;
                        $errors[] = "Row " . ($index + 1) . ": NISN dan nama lengkap wajib diisi";
                        continue;
                    }

                    // Check if NISN already exists
                    if ($this->studentRepository->nisnExists($row['nisn'])) {
                        $failed++;
                        $errors[] = "Row " . ($index + 1) . ": NISN {$row['nisn']} sudah terdaftar";
                        continue;
                    }

                    // Prepare student data
                    $data = [
                        'school_id' => $row['school_id'] ?? 1,
                        'nisn' => $row['nisn'],
                        'nis' => $row['nis'] ?? null,
                        'full_name' => $row['full_name'],
                        'gender' => $row['gender'] ?? 'L',
                        'birth_place' => $row['birth_place'] ?? null,
                        'birth_date' => $row['birth_date'] ?? null,
                        'religion' => $row['religion'] ?? null,
                        'address' => $row['address'] ?? null,
                        'phone' => $row['phone'] ?? null,
                        'previous_school' => $row['previous_school'] ?? null,
                        'guardian_name' => $row['guardian_name'] ?? null,
                        'guardian_phone' => $row['guardian_phone'] ?? null,
                        'guardian_occupation' => $row['guardian_occupation'] ?? null,
                        'guardian_address' => $row['guardian_address'] ?? null,
                        'hobby' => $row['hobby'] ?? null,
                        'health_history' => $row['health_history'] ?? null,
                        'entry_year' => $row['entry_year'] ?? date('Y'),
                        'email' => $row['email'] ?? null,
                    ];

                    $this->createStudentWithUser($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Generate unique email
     */
    private function generateUniqueEmail(string $baseEmail): string
    {
        $email = $baseEmail;
        $emailBase = preg_replace('/@.+$/', '', $email);
        $domain = preg_match('/@(.+)$/', $email, $matches) ? $matches[1] : 'students.local';
        $i = 1;

        while (User::where('email', $email)->exists()) {
            $email = $emailBase . '+' . $i . '@' . $domain;
            $i++;
        }

        return $email;
    }

    /**
     * Generate unique username
     */
    private function generateUniqueUsername(string $baseUsername): string
    {
        $username = $baseUsername;
        $i = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $i;
            $i++;
        }

        return $username;
    }
}
