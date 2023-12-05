<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
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
        ]);
        $departmentNames = ['Financial', 'Managerial', 'Worker', 'TeachingStuff'];

        foreach ($departmentNames as $name) {
            Department::create(['name' => $name]);
        }

        $roleNames = ['Manager', 'Teacher', 'Worker', 'Employee'];
        foreach ($roleNames as $name) {
            Role::create(['name' => $name]);
        }

        Employee::create([
            'user_id' => 1,
            'role_id' => 1,
            'department_id' => 1,
            'basic_salary' => 5000
        ]);

    }
}
