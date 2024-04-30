<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'Mathematics',
            'Physics',
            'Chemistry',
            'Biology',
            'History',
            'Literature',
            'Geography',
            'Economics',
            'Computer Science',
            'Psychology',
        ];

        // Loop through subject names and create individual records
        foreach ($subjects as $subjectName) {
            Subject::create([
                'name' => $subjectName,
            ]);
        }
    }
}
