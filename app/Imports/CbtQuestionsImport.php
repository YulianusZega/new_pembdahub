<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class CbtQuestionsImport implements WithMultipleSheets
{
    protected CbtQuestionsSheetImport $sheetImport;

    public function __construct()
    {
        $this->sheetImport = new CbtQuestionsSheetImport();
    }

    public function sheets(): array
    {
        // Hanya proses sheet pertama (index 0) yang berisi data soal
        return [
            0 => $this->sheetImport,
        ];
    }

    public function getRows(): array
    {
        return $this->sheetImport->getRows();
    }
}

class CbtQuestionsSheetImport implements ToCollection, WithHeadingRow
{
    protected array $rows = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip completely empty rows
            $rowArray = $row->toArray();
            $hasContent = collect($rowArray)->filter(fn($v) => !is_null($v) && trim((string) $v) !== '')->isNotEmpty();

            if ($hasContent) {
                $this->rows[] = $rowArray;
            }
        }
    }

    public function getRows(): array
    {
        return $this->rows;
    }
}
