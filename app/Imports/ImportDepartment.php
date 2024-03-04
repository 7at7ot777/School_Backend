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

    public $counter = 0;
    public function __construct()
    {
        $counter = 0;
    }

    public function model(array $row)
    {
        $departmentName = $row['department_name'];

        // Check if the department already exists in the database
        $existingDepartment = Department::where('name', $departmentName)->first();

        if (!$existingDepartment) {
            $this->counter++;

            // Department doesn't exist, so create a new one
            return new Department([
                'name' => $departmentName,
            ]);
        }


        return null; // Returning null skips the insertion for existing departments
    }
}
