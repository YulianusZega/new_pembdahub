<?php

namespace App\Exports;

use App\Models\Applicant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApplicantsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Applicant::with(['school', 'academicYear', 'programKeahlian', 'konsentrasiKeahlian'])
            ->orderBy('created_at', 'desc');

        if (!empty($this->filters['academic_year_id'])) {
            $query->where('academic_year_id', $this->filters['academic_year_id']);
        }
        if (!empty($this->filters['school_id'])) {
            $query->where('school_id', $this->filters['school_id']);
        }
        if (!empty($this->filters['program_keahlian_id'])) {
            $query->where('program_keahlian_id', $this->filters['program_keahlian_id']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Registrasi',
            'NISN',
            'Nama Lengkap',
            'L/P',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Alamat',
            'Telepon',
            'Email',
            'Unit Sekolah',
            'Program Keahlian',
            'Konsentrasi Keahlian',
            'Jalur',
            'Asal Sekolah',
            'Nama Ayah',
            'Nama Ibu',
            'Status',
            'Tanggal Daftar',
        ];
    }

    public function map($applicant): array
    {
        static $counter = 0;
        $counter++;

        return [
            $counter,
            $applicant->registration_number,
            $applicant->nisn,
            $applicant->full_name,
            $applicant->gender,
            $applicant->birth_place,
            $applicant->birth_date ? $applicant->birth_date->format('d/m/Y') : '-',
            $applicant->religion,
            $applicant->address,
            $applicant->phone ?? '-',
            $applicant->email ?? '-',
            $applicant->school->name,
            $applicant->programKeahlian?->nama ?? '-',
            $applicant->konsentrasiKeahlian?->nama ?? '-',
            ucfirst($applicant->admission_path),
            $applicant->previous_school,
            $applicant->father_name,
            $applicant->mother_name,
            $applicant->getStatusLabel(),
            $applicant->submission_date ? $applicant->submission_date->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
