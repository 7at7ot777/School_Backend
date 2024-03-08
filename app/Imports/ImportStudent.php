<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportStudent implements ToModel, WithHeadingRow
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
        //Check if student is not added
        $newUser = User::where('email', 'LIKE', $row['email'])->first();
        if (!$newUser) {
            // Create a new User Student
            $newUser = new User([
                'name' => $row['name'] ?? ' ',
                'phone' => $row['phone'] ?? ' ',
                'address' => $row['address'] ?? ' ',
                'password' => bcrypt('welcome'),
                'email' => $row['email'] ?? ' ',
                'user_type' => 'student',
            ]);
            $newUser->save();
            // Create a new Employee

            $newStudent = new Student([
                'user_id' => $newUser->id,
                'father_id' => $this->getFatherId($row),
                'mother_id' =>$this->getMotherId($row),
                'grade_level' => $row['grade'] ?? 1,
                'class_id' => 1,
            ]);
            $newStudent->save();
            $this->counter++;
            return $newStudent;
        }
return null;


    }

    public function getFatherId($row)
    {
        $fatherId = User::where('email', 'LIKE', $row['father_email'])->first();
        if($fatherId)
            return $fatherId->id;
        else{

            $newFather = new User([
                'name' => $row['father_name'] ?? ' ',
                'phone' => $row['father_phone'] ?? ' ',
                'address' => $row['address'] ?? ' ',
                'password' => bcrypt('welcome'),
                'email' => $row['father_email'] ?? ' ',
                'user_type' => 'parent',
            ]);
            $newFather->save();
            return $newFather->id;
        }


    }

    public function getMotherId($row)
    {
        $motherId = User::where('email', 'LIKE', $row['mother_email'])->first();
        if($motherId)
            return $motherId->id;

        $newMother = new User([
            'name' => $row['mother_name'] ?? ' ',
            'phone' => $row['mother_phone'] ?? ' ',
            'address' => $row['address'] ?? ' ',
            'password' => bcrypt('welcome'),
            'email' => $row['mother_email'] ?? ' ',
            'user_type' => 'parent',
        ]);
        $newMother->save();
        return $newMother->id;

    }
}
