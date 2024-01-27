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
        $departmentName = $row['department_name'];

        // Check if the department already exists in the database
        $existingDepartment = Department::where('name', $departmentName)->first();

        if (!$existingDepartment) {
            // Department doesn't exist, so create a new one
            return new Department([
                'name' => $departmentName,
            ]);
        }

        // Department already exists, you can handle this case if needed
        // For example, you might want to update the existing department instead of creating a new one.
        // You can add your logic here.

        return null; // Returning null skips the insertion for existing departments
    }
}
