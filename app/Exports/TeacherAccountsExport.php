<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeacherAccountsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $schoolId;

    public function __construct($schoolId = null)
    {
        $this->schoolId = $schoolId;
    }

    public function collection()
    {
        $query = Teacher::with(['user', 'school']);
        
        if ($this->schoolId) {
            $query->where('school_id', $this->schoolId);
        }

        return $query->orderBy('full_name')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA LENGKAP',
            'KODE GURU',
            'UNIT SEKOLAH',
            'USERNAME',
            'PASSWORD (POLA)',
        ];
    }

    public function map($teacher): array
    {
        static $row = 0;
        $row++;
        
        $code = $teacher->teacher_code ?: $teacher->id;

        return [
            $row,
            $teacher->full_name,
            $teacher->teacher_code,
            $teacher->school ? $teacher->school->name : '-',
            $teacher->user ? $teacher->user->username : '-',
            'Pembda' . $code,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
