<?php

namespace App\Exports;

use App\Models\StudentBill;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BillsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $academicYearId;
    protected $paymentTypeId;
    protected $search;

    public function __construct($academicYearId = null, $paymentTypeId = null, $search = null)
    {
        $this->academicYearId = $academicYearId ?: (\App\Models\AcademicYear::where('is_active', true)->first()?->id
            ?? \App\Models\AcademicYear::orderBy('year', 'desc')->first()?->id);
        $this->paymentTypeId = $paymentTypeId;
        $this->search = $search;
    }

    public function collection()
    {
        $query = StudentBill::with(['student', 'paymentType', 'academicYear']);

        if ($this->academicYearId) {
            $query->where('academic_year_id', $this->academicYearId);
        }

        if ($this->paymentTypeId) {
            $query->where('payment_type_id', $this->paymentTypeId);
        }

        if ($this->search) {
            $query->whereHas('student', function ($q) {
                $q->where('full_name', 'like', "%{$this->search}%")
                  ->orWhere('nisn', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('student_id')
            ->orderBy('payment_type_id')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NISN',
            'Nama Siswa',
            'Jenis Tagihan',
            'Tahun Ajaran',
            'Bulan',
            'Tahun',
            'Jatuh Tempo',
            'Jumlah Tagihan',
            'Sudah Dibayar',
            'Sisa Tunggakan',
            'Status',
        ];
    }

    public function map($bill): array
    {
        static $counter = 0;
        $counter++;

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $statusLabels = [
            'belum_bayar' => 'Belum Bayar',
            'cicilan' => 'Cicilan',
            'lunas' => 'Lunas',
        ];

        return [
            $counter,
            $bill->student->nisn,
            $bill->student->full_name,
            $bill->paymentType->type_name,
            $bill->academicYear->year,
            $monthNames[$bill->month] ?? '-',
            $bill->year,
            $bill->due_date ? $bill->due_date->format('d/m/Y') : '-',
            $bill->amount,
            $bill->paid_amount,
            $bill->amount - $bill->paid_amount,
            $statusLabels[$bill->status] ?? $bill->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
