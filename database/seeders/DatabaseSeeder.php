<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ClassRoom;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //         \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('123456'),
            'user_type' => 'employee'
        ]);

        \App\Models\User::create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'phone' => '123456',
            'password' => bcrypt('123456'),
            'user_type' => 'student'
        ]);

        \App\Models\User::create([
            'name' => 'father',
            'email' => 'father@example.com',
            'phone' => '123456',

        'password' => bcrypt('123456'),
            'user_type' => 'parent'
        ]);

        \App\Models\User::create([
            'name' => 'mother User',
            'email' => 'mother@example.com',
            'phone' => '123456',

            'password' => bcrypt('123456'),
            'user_type' => 'parent'
        ]);


            Department::insert([
                [
                    'id' => 1,
                    'name' => 'Financial'
                ] ,[
                    'id' => 2,
                    'name' => 'Managerial'
                ], [
                    'id' => 3,
                    'name' => 'Worker'
                ], [
                    'id' => 4,
                    'name' => 'Teaching Staff'
                ] ,[
                    'id' => 5,
                    'name' => 'Student Affairs'
                ],
            ]);


        $roleNames = ['Manager', 'Teacher', 'Worker', 'Employee'];
        foreach ($roleNames as $name) {
            Role::create(['name' => $name]);
        }

        Employee::create([
            'user_id' => 1,
//            'role_id' => 1,
            'role' => 'superAdmin',
            'department_id' => null,
            'basic_salary' => 5000
        ]);

        Subject::create(
            ['name' => 'arabic' ],
        );

        Student::create([
            'user_id' => 2,
            'grade_level' => 10,
            'father_id' => 3,
            'mother_id' => 4,
            'class_id' => 1,
            'semester' => 1,
        ]);

        ClassRoom::create([
            'class_number' => 101,
            'grade' => 10,
        ]);

        ClassRoom::create([
            'class_number' => 201,
            'grade' => 11,
        ]);


    }
}
