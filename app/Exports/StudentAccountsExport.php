<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Classroom;
use App\Models\School;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentAccountsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $classroomId;
    protected $schoolId;

    public function __construct($classroomId = null, $schoolId = null)
    {
        $this->classroomId = $classroomId;
        $this->schoolId = $schoolId;
    }

    public function collection()
    {
        $query = Student::with(['user', 'school']);
        
        if ($this->classroomId) {
            $query->whereHas('studentClasses', function($q) {
                $q->where('classroom_id', $this->classroomId);
            });
        } elseif ($this->schoolId) {
            $query->where('school_id', $this->schoolId);
        }

        return $query->orderBy('full_name')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA LENGKAP',
            'NISN',
            'UNIT SEKOLAH',
            'USERNAME',
            'PASSWORD (POLA)',
        ];
    }

    public function map($student): array
    {
        static $row = 0;
        $row++;
        
        return [
            $row,
            $student->full_name,
            $student->nisn,
            $student->school ? $student->school->name : '-',
            $student->user ? $student->user->username : $student->nisn,
            'Pembda' . $student->nisn,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
