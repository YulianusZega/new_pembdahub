<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BillsReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithCustomStartCell, WithEvents
{
    protected $bills;
    protected $filters;
    protected $showSchoolColumn;

    public function __construct($bills, $filters)
    {
        $this->bills = $bills;
        $this->filters = $filters;
        // Show school column if SuperAdmin (all schools) or explicitly requested
        $this->showSchoolColumn = $filters['show_school_column'] ?? false;
    }

    public function startCell(): string
    {
        return 'A6'; // Start data after header info (rows 1-5 for filter info)
    }

    public function collection()
    {
        // Group bills by student
        $studentsData = $this->bills->groupBy('student_id')->map(function ($studentBills) {
            $student = $studentBills->first()->student;
            $academicYearId = $this->filters['academic_year']?->id;
            $classroom = $student->classrooms->where('pivot.academic_year_id', $academicYearId)->first();

            $monthlyBills = [];
            foreach ($studentBills as $bill) {
                $monthlyBills[$bill->month] = $bill;
            }

            $totalAmount      = $studentBills->sum('amount');
            $totalPaid        = $studentBills->sum('paid_amount');
            $totalOutstanding = max(0, $totalAmount - $totalPaid);

            return [
                'student'           => $student,
                'classroom'         => $classroom,
                'monthly_bills'     => $monthlyBills,
                'total_amount'      => $totalAmount,
                'total_paid'        => $totalPaid,
                'total_outstanding' => $totalOutstanding,
            ];
        });

        // Sort by classroom then name
        $studentsData = $studentsData->sortBy(function ($data) {
            return [
                $data['student']->status != 'aktif' ? 1 : 0,
                $data['classroom']?->class_name ?? 'zzz',
                $data['student']->full_name,
            ];
        });

        return $studentsData;
    }

    public function headings(): array
    {
        $baseHeadings = ['No', 'Nama Siswa', 'NISN', 'Kelas'];

        if ($this->showSchoolColumn) {
            $baseHeadings[] = 'Sekolah';
        }

        return array_merge($baseHeadings, [
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Total Tagihan (Rp)', 'Terbayar (Rp)', 'Tunggakan (Rp)',
        ]);
    }

    public function map($studentData): array
    {
        static $no = 0;
        $no++;

        $student      = $studentData['student'];
        $classroom    = $studentData['classroom'];
        $monthlyBills = $studentData['monthly_bills'];

        // Map months 7-12 (Jul-Des), 1-6 (Jan-Jun)
        $monthOrder = [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6];
        $monthColumns = [];

        foreach ($monthOrder as $month) {
            if (isset($monthlyBills[$month])) {
                $bill = $monthlyBills[$month];
                if ($bill->status == 'lunas') {
                    $monthColumns[] = '✓';
                } elseif ($bill->status == 'cicilan') {
                    $monthColumns[] = '◑';
                } else {
                    $monthColumns[] = '✗';
                }
            } else {
                $monthColumns[] = '-';
            }
        }

        $row = [
            $no,
            $student->full_name,
            $student->nisn,
            $classroom?->class_name ?? '-',
        ];

        if ($this->showSchoolColumn) {
            $row[] = $student->school?->name ?? '-';
        }

        return array_merge($row, $monthColumns, [
            $studentData['total_amount'],
            $studentData['total_paid'],
            $studentData['total_outstanding'],
        ]);
    }

    public function title(): string
    {
        return 'Laporan Pembayaran';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            6 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Determine last column based on showSchoolColumn
                // Columns: No, Nama, NISN, Kelas, [Sekolah?], 12 months, 3 totals
                $baseCount = $this->showSchoolColumn ? 5 : 4;
                $totalCols = $baseCount + 12 + 3; // base + months + totals
                $lastCol = $this->numberToColumn($totalCols);

                // Title
                $sheet->setCellValue('A1', 'LAPORAN PROGRESS PEMBAYARAN SPP');
                $sheet->mergeCells('A1:' . $lastCol . '1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Filter info rows
                $row = 3;
                if (!empty($this->filters['school'])) {
                    $sheet->setCellValue('A' . $row, 'Sekolah: ' . $this->filters['school']->name);
                    $row++;
                }
                if (!empty($this->filters['academic_year'])) {
                    $sheet->setCellValue('A' . $row, 'Tahun Ajaran: ' . $this->filters['academic_year']->year);
                    $row++;
                }
                if (!empty($this->filters['classroom'])) {
                    $sheet->setCellValue('A' . $row, 'Kelas: ' . $this->filters['classroom']->class_name);
                    $row++;
                }
                if (!empty($this->filters['payment_type'])) {
                    $sheet->setCellValue('A' . $row, 'Jenis Tagihan: ' . $this->filters['payment_type']->type_name);
                    $row++;
                }

                // Style filter rows
                for ($i = 3; $i < $row; $i++) {
                    $sheet->mergeCells('A' . $i . ':F' . $i);
                    $sheet->getStyle('A' . $i)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                    ]);
                }

                // Auto-size columns
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Center month columns (Jul-Jun = 5 or 6 cols onward)
                $monthStartCol = $this->numberToColumn($baseCount + 1);
                $monthEndCol   = $this->numberToColumn($baseCount + 12);
                $dataEndRow    = $sheet->getHighestRow();
                $sheet->getStyle($monthStartCol . '6:' . $monthEndCol . $dataEndRow)
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Number format for total columns
                $totalStartCol = $this->numberToColumn($baseCount + 13);
                $totalEndCol   = $lastCol;
                if ($dataEndRow >= 7) {
                    $sheet->getStyle($totalStartCol . '7:' . $totalEndCol . $dataEndRow)
                        ->getNumberFormat()->setFormatCode('#,##0');
                }

                // Green fill for lunas (✓) cells
                $monthStartColIdx = $baseCount + 1;
                $monthEndColIdx   = $baseCount + 12;
                for ($r = 7; $r <= $dataEndRow; $r++) {
                    for ($c = $monthStartColIdx; $c <= $monthEndColIdx; $c++) {
                        $colLetter = $this->numberToColumn($c);
                        $cellValue = $sheet->getCell($colLetter . $r)->getValue();
                        if ($cellValue === '✓') {
                            $sheet->getStyle($colLetter . $r)->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
                                'font' => ['color' => ['rgb' => '059669']],
                            ]);
                        } elseif ($cellValue === '✗') {
                            $sheet->getStyle($colLetter . $r)->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
                                'font' => ['color' => ['rgb' => 'DC2626']],
                            ]);
                        } elseif ($cellValue === '◑') {
                            $sheet->getStyle($colLetter . $r)->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
                                'font' => ['color' => ['rgb' => 'D97706']],
                            ]);
                        }
                    }
                }

                // Border all data
                $sheet->getStyle('A6:' . $lastCol . $dataEndRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                    ],
                ]);

                // Freeze panes: freeze name column
                $sheet->freezePane('E7');
            },
        ];
    }

    /**
     * Convert column number (1-based) to Excel column letter (A, B, ..., Z, AA, AB, ...)
     */
    private function numberToColumn(int $number): string
    {
        $column = '';
        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $column = chr(65 + $remainder) . $column;
            $number = intdiv($number - 1, 26);
        }
        return $column;
    }
}
