<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class AdminManageEmployeeController extends Controller
{
    public function createEmployee(Request $request)
    {
        // قم بتحقق من وجود subject_id لتحديد الدور (teacher أو employee)
        $role = $request->has('subject_id') ? 'teacher' : 'employee';

        // قم بإنشاء مستخدم جديد
        $user = User::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'password' => bcrypt('welcome'),
            'email' => $request->input('email'),
            'user_type' => 'employee'
        ]);

        // قم بحفظ المستخدم
        $user->save();

        // قم بإنشاء موظف جديد وتحديد الدور والبيانات الأخرى
        $employee = Employee::create([
            'user_id' => $user->id,
            'department_id' => $request->input('department_id'),
            'basic_salary' => $request->input('basic_salary'),
            'role' => $role,
            'subject_id' => $request->input('subject_id')
        ]);

        // قم بحفظ الموظف
        $employee->save();

        // قم بإرجاع رسالة نجاح
        return response()->json(['message' => 'Employee created successfully'], 201);
    }

    public function index($dept_id)
{
    // ابحث عن جميع الموظفين في القسم المحدد مع معلومات المستخدم المرتبطة
    $employees = Employee::with('user')
        ->where('department_id', $dept_id)
        ->where(function ($query) {
            $query->where('role', 'teacher')
                ->orWhere('role', 'employee');
        })
        ->get();

    // إذا لم يتم العثور على موظفين، قم بإرجاع رسالة خطأ
    if ($employees->isEmpty()) {
        return response()->json(['error' => 'No employees found in the specified department'], 404);
    }

    // قم بتنسيق معلومات الموظفين وإرجاعها
    $formattedEmployees = $employees->map(function ($employee) {
        return [
            'id' => $employee->id,
            'name' => $employee->user->name,
            'email' => $employee->user->email,
            'phone' => $employee->user->phone,
            'address' => $employee->user->address,
            'department_id' => $employee->department_id,
            'basic_salary' => $employee->basic_salary,
            'is_active' => $employee->is_active,
            'role' => $employee->role,
            'subject_id' => $employee->subject_id,
        ];
    });

    return response()->json($formattedEmployees, 200);
}




    public function updateEmployee(Request $request, $id)
    {
        // ابحث عن الموظف المراد تحديثه
        $employee = Employee::find($id);

        // إذا لم يتم العثور على الموظف، قم بإرجاع رسالة خطأ
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // قم بتحديث معلومات الموظف
        $employee->department_id = $request->input('department_id');
        $employee->basic_salary = $request->input('basic_salary');
        $employee->subject_id = $request->input('subject_id');

        // قم بحفظ التغييرات
        $employee->save();

        // قم بتحديث معلومات المستخدم إذا كانت هناك تغييرات
        $user = $employee->user;
        if ($user) {
            $user->name = $request->input('name');
            $user->phone = $request->input('phone');
            $user->address = $request->input('address');
            $user->email = $request->input('email');
            $user->save();
        }

        // قم بإرجاع رسالة نجاح
        return response()->json(['message' => 'Employee updated successfully'], 200);
    }

    public function deleteEmployee($id)
    {
        $employee = Employee::find($id);

    if (!$employee) {
        return response()->json(['error' => 'Employee not found'], 404);
    }
    
    $employee->delete(); // يقوم بتحديث deleted_at بتاريخ الحذف

    return response()->json(['message' => 'Employee deleted successfully'], 200);
    }

}
