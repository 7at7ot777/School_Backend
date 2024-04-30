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

        $this->call([
            UsersTableSeeder::class,
            DepartmentTableSeeder::class,
            RolesTableSeeder::class,
            EmployeeTableSeeder::class,
            SubjectSeeder::class,
            StudentSeeder::class,
            StudentGradesSeeder::class
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
