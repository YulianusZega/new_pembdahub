<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuizQuestionsTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'question' => 'Berapakah hasil dari $2 + 3 \\times 4$?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'B',
                'score' => 10,
                'option_a' => '20',
                'option_b' => '14',
                'option_c' => '18',
                'option_d' => '24',
                'option_e' => '28'
            ],
            [
                'question' => 'Apakah air membeku pada suhu $0^\\circ \\text{C}$?',
                'question_type' => 'true_false',
                'correct_answer' => 'true',
                'score' => 10,
                'option_a' => '',
                'option_b' => '',
                'option_c' => '',
                'option_d' => '',
                'option_e' => ''
            ],
            [
                'question' => 'Jelaskan hukum Newton ke-2 dan tuliskan rumusnya!',
                'question_type' => 'essay',
                'correct_answer' => '',
                'score' => 20,
                'option_a' => '',
                'option_b' => '',
                'option_c' => '',
                'option_d' => '',
                'option_e' => ''
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'question',
            'question_type',
            'correct_answer',
            'score',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'option_e'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '7C3AED'] // LMS Purple theme color
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
