<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

    }
}
