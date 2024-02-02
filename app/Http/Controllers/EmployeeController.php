<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
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

    public function employeesWithinDepartment($dept_id)
    {
        // ابحث عن جميع الموظفين في القسم المحدد مع معلومات المستخدم المرتبطة
         $employees = Employee::with('department:id,name', 'user:id,email,name,phone,status','subject')
            ->where(function ($query) {
                $query->where('role', 'teacher')
                    ->orWhere('role', 'employee');
            })
            ->where('department_id',$dept_id)
            ->get();


        // قم بتنسيق معلومات الموظفين وإرجاعها
        $formattedEmployees = $this->formatDate($employees);

        return response()->json($formattedEmployees, 200);
    }

    public function index()
    {
        // ابحث عن جميع الموظفين في القسم المحدد مع معلومات المستخدم المرتبطة
         $employees = Employee::with('department:id,name', 'user:id,email,name,phone,status','subject')
            ->where(function ($query) {
                $query->where('role', 'teacher')
                    ->orWhere('role', 'employee');
            })
            ->get();


        // قم بتنسيق معلومات الموظفين وإرجاعها
        $formattedEmployees = $this->formatDate($employees);

        return response()->json($formattedEmployees, 200);
    }

    private function formatDate($data)
    {
        $resultArray = [];

        foreach ($data as $item) {

            $sub_id = null;
            $sub_name = null;
            $resultArray[] = [
                'id' => $item['id'],
                'avatarUrl' => '', // Add logic to get the avatar URL if available
                'name' => $item['user']['name'],
                'email' => $item['user']['email'],
                'status' => $item['user']['status'],
                'role' => $item['role'],
                'basic_salary' => $item['basic_salary'],
                'subject' =>[
                    'id' => $item['subject']['id'],
                    'name' => $item['subject']['name'],
                ]
//                'department' => [
//                    'id' => $item['department']['id'] ?? $dept_id,
//                    'name' => $item['department']['name'] ?? $dept_name,
//                ],
            ];
        }
        return $resultArray;
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

    public function toggleIsActive($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $user->status = $user->status == 0 ? 1 : 0;

        $user->save();

        $status = $user->status == 1 ? 'active' : 'inactive';
        return response()->json(['message' => "Employee status toggled successfully. Now the employee is $status"], 200);
    }
}
