<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Student::create([
            'user_id' => 2,
            'grade_level' => 10,
            'father_id' => 3,
            'mother_id' => 4,
            'class_id' => 1,
            'semester' => 1,
        ]);

    }
}
