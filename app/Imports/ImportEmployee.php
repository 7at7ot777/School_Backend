<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportEmployee implements  ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */

    public $counter = 0;
    public $dept_id;

    public function __construct($dept_id)
    {
        $this->counter = 0;
        $this->dept_id = $dept_id;
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
            $newEmployee = new Employee();
            $newEmployee->user_id = $newUser->id;
            $newEmployee->department_id =  $this->dept_id;
            $newEmployee->basic_salary =$row['basic_salary'] ?? 0;
            if($this->dept_id == 4)
            {
                $newEmployee->role = 'teacher';
            }else{
                $newEmployee->role = 'employee';

            }

            $newEmployee->save();
            $this->counter++;


            return $newEmployee;
        }
        return null;

    }
}
