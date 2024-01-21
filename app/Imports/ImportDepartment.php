<?php

namespace App\Imports;

use App\Models\Department;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportDepartment implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */


    public function model(array $row)
    {
        return new Department([
            'name' => $row['department_name']
        ]);
    }
}
