<?php

namespace App\Imports;

use App\Models\ClassRoom;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportClassroom implements ToModel, WithHeadingRow
{
    public $counter = 0;

    public function __construct()
    {
        $counter = 0;
    }

    public function model(array $row)
    {
        $this->counter++;

        return new ClassRoom([
            'class_number' => $row['class_number'],
            'grade' => $row['grade'],
        ]);

    }
}
