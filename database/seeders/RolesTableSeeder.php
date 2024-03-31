<?php

namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleNames = ['Manager', 'Teacher', 'Worker', 'Employee'];
        foreach ($roleNames as $name) {
            Role::create(['name' => $name]);
        }
    }
}
