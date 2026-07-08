<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentSampleExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'school_id' => 1,
                'nisn' => '20210001',
                'nis' => '1001',
                'full_name' => 'Andi Wijaya',
                'gender' => 'L',
                'birth_place' => 'Medan',
                'birth_date' => '2008-05-01',
                'religion' => 'Islam',
                'previous_school' => 'SMP Negeri 1',
                'guardian_name' => 'Ayah Andi',
                'guardian_phone' => '08123456789',
                'guardian_occupation' => 'Pegawai Negeri',
                'guardian_address' => 'Jl. Merdeka 1',
                'hobby' => 'Sepakbola',
                'health_history' => 'Sehat',
                'entry_year' => 2021,
                'address' => 'Jl. Merdeka 1',
                'email' => 'andi@example.com'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'school_id',
            'nisn',
            'nis',
            'full_name',
            'gender',
            'birth_place',
            'birth_date',
            'religion',
            'previous_school',
            'guardian_name',
            'guardian_phone',
            'guardian_occupation',
            'guardian_address',
            'hobby',
            'health_history',
            'entry_year',
            'address',
            'email'
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
