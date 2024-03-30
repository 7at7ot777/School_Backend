<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;


class StudentGradesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $studentIds = Student::pluck('id')->toArray();
        $subjectIds = Subject::pluck('id')->toArray();

        for ($i = 0; $i < 10; $i++) {
            DB::table('student_grades')->insert([
                'student_id' => $faker->randomElement($studentIds),
                'subject_id' => $faker->randomElement($subjectIds),
                'level' => $faker->numberBetween(0, 13),
                'midterm' => $faker->numberBetween(0, 100),
                'final' => $faker->numberBetween(0, 100),
                'attendance' => $faker->numberBetween(0, 100),
                'behavior' => $faker->numberBetween(0, 100),
                'total' => $faker->numberBetween(0, 100),
                'created_at' => now(),
            ]);
        }
    }
}
