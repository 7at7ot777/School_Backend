<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Employee;

class SuperAdminDashboardController extends Controller
{
    public function dashboard()
    {
        $departments = Department::all();

        $departmentData = [];

        foreach ($departments as $department) {
            $departmentId = $department->id;
            $departmentName = $department->name;

            $adminCount = Employee::where('department_id', $departmentId)
                ->where('role', 'admin')
                ->count();

            $employeeCount = Employee::where('department_id', $departmentId)
                ->where('role', 'employee')
                ->count();

            $mainAdmin = Employee::where('department_id', $departmentId)
                ->where('role', 'superAdmin')
                ->first();

            $departmentData[] = [
                'id' => $departmentId,
                'name' => $departmentName,
                'numOfAdmins' => $adminCount,
                'numOfEmps' => $employeeCount,
                'mainAdmin' => [
                    'name' => $mainAdmin ? $mainAdmin->name : null,
                    'avatarUrl' => $mainAdmin ? $mainAdmin->avatar_url : null,
                ],
            ];
        }

        return response()->json(['data' => $departmentData]);
    }
}
