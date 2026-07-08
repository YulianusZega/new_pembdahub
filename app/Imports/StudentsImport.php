<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class StudentsImport implements ToCollection, WithHeadingRow
{
    protected array $rows = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->rows[] = $row->toArray();
        }
    }

    public function getRows(): array
    {
        return $this->rows;
    }
}
