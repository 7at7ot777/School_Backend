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

            // Count admins where status is true
            $countAdmins = Employee::where('role', 'admin')
                ->whereHas('user', function ($query) {
                    $query->where('status', true);
                })
                ->count();

// Count employees where status is true
            $countEmployees = Employee::where('role', 'employee')
                ->whereHas('user', function ($query) {
                    $query->where('status', true);
                })
                ->count();

            $mainAdmin = Employee::where('department_id', $departmentId)
                ->where('role', 'superAdmin')
                ->first();

            $departmentData[] = [
                'id' => $departmentId,
                'name' => $departmentName,
                'numOfAdmins' => $countAdmins,
                'numOfEmps' => $countEmployees,
                'mainAdmin' => [
                    'name' => $mainAdmin ? $mainAdmin->name : null,
                    'avatarUrl' => $mainAdmin ? $mainAdmin->avatar_url : null,
                ],
            ];
        }

        return response()->json(['data' => $departmentData]);
    }
}
