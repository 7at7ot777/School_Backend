<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create([
            'user_id' => 1,
            'role' => 'superAdmin',
            'department_id' => null,
            'basic_salary' => 5000
        ]);  Employee::create([
        'user_id' => 5,
        'role' => 'teacher',
        'department_id' => 4,
        'basic_salary' => 5000
    ]);
    }
}
