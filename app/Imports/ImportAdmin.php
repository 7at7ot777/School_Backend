<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportAdmin implements ToModel, WithHeadingRow
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
//        dd($row);
        if (!isset($row['name']))
            return null;
        // Create a new User
        $newUser = User::where('email', 'LIKE', $row['email'])->first();
        if (!$newUser) {
            $newUser = new User([
                'name' => $row['name'] ?? ' ',
                'phone' => $row['phone'] ?? ' ',
                'address' => $row['address'] ?? ' ',
                'password' => bcrypt('welcome'),
                'email' => $row['email'] ?? ' ',
                'user_type' => 'employee',
            ]);
            $newUser->save();
            // Create a new Employee
            $newEmployee = new Employee([
                'user_id' => $newUser->id,
                'department_id' => Department::where('name', 'LIKE', $row['department'])->first()->id ?? null,
                'basic_salary' => $row['basic_salary'] ?? null,
                'role' => 'admin',
                'subject_id' => null, // You might need to adjust this based on your actual data
            ]);
            $newEmployee->save();
            $this->counter++;


            return $newEmployee;
        }
        return null;

    }
}
