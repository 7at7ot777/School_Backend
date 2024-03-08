<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('123456'),
            'user_type' => 'employee'
        ]);

        \App\Models\User::create([
            'name' => 'Student Hathout',
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

        \App\Models\User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'password' => bcrypt('123456'),
            'user_type' => 'employee'
        ]);

    }
}
