<?php
require __DIR__.'/vendor/autoload.php';
\ = require_once __DIR__.'/bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();

\ = App\Models\Teacher::where('full_name', 'like', '%YONATA%')->first();
if (!\) { echo 'Teacher not found'; exit; }
\ = App\Models\Classroom::where('homeroom_teacher_id', \->id)->get(['id', 'class_name', 'is_active', 'academic_year_id'])->toArray();
\ = App\Models\AcademicYear::where('is_active', true)->first();

echo json_encode(['teacher_id' => \->id, 'classrooms' => \, 'activeYear_id' => \->id ?? null], JSON_PRETTY_PRINT);
