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
            'password' => bcrypt($request->input('password')),
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

    public function readEmployee($id)
    {
        // ابحث عن معلومات الموظف بناءً على الرقم المعرف
        $employee = Employee::with('user')->find($id);

        // إذا لم يتم العثور على الموظف، قم بإرجاع رسالة خطأ
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // قم بتنسيق معلومات الموظف وإرجاعها
        $formattedEmployee = [
            'id' => $employee->id,
            'name' => $employee->user->name,
            'email' => $employee->user->email,
            'phone' => $employee->user->phone,
            'address' => $employee->user->address,
            'department_id' => $employee->department_id,
            'basic_salary' => $employee->basic_salary,
            'role' => $employee->role,
            'subject_id' => $employee->subject_id,
        ];

        return response()->json($formattedEmployee, 200);
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
        // ابحث عن الموظف المراد حذفه
        $employee = Employee::find($id);

        // إذا لم يتم العثور على الموظف، قم بإرجاع رسالة خطأ
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // حذف الموظف
        $employee->delete();

        // قم بإرجاع رسالة نجاح
        return response()->json(['message' => 'Employee deleted successfully'], 200);
    }

}
