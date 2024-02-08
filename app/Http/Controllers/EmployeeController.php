<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public static $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'password' => 'nullable|string|max:255',
        'email' => 'required|email|unique:users|max:255',
        //========================================================
        //'role_id' => 'required|exists:roles,id',
        //'role' => 'required|in:admin,superAdmin,employee,teacher',
        'department_id' => 'required|exists:departments,id',
//        'basic_salary' => 'required|integer',
        'subject_id' => 'nullable|exists:subjects,id'
    ];

    public static $errorMessages = [
        'name.required' => 'The name field is required.',
        'name.string' => 'The name field must be a string.',
        'name.max' => 'The name field must not exceed 255 characters.',

        'phone.string' => 'The phone field must be a string.',
        'phone.max' => 'The phone field must not exceed 255 characters.',

        'address.string' => 'The address field must be a string.',
        'address.max' => 'The address field must not exceed 255 characters.',

        'password.required' => 'The password field is required.',
        'password.string' => 'The password field must be a string.',
        'password.max' => 'The password field must not exceed 255 characters.',

        'email.required' => 'The email field is required.',
        'email.email' => 'The email must be a valid email address.',
        'email.unique' => 'The specified email address is already taken.',
        'email.max' => 'The email field must not exceed 255 characters.',

        'department_id.required' => 'The department ID field is required.',
        'department_id.exists' => 'The selected department does not exist.',

        'basic_salary.required' => 'The basic salary field is required.',
        'basic_salary.integer' => 'The basic salary must be an integer.',

        'subject_id.exists' => 'The selected subject does not exist.',

        'role.required' => 'The role field is required.',
        'role.in' => 'Invalid value for the role field. Allowed values are admin, superAdmin, employee, teacher.',

    ];
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
        if ($validator->fails()) {
            return $validator->errors();
        }
        // قم بتحقق من وجود subject_id لتحديد الدور (teacher أو employee)
        $role = $request->subject_id != null ? 'teacher' : 'employee';

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

    public function index($dept_id = null)
    {
//        return $dept_id;
        if (!isset($dept_id))
              return response()->json(['error' => 'Invalid department'], 404);

            // ابحث عن جميع الموظفين في القسم المحدد مع معلومات المستخدم المرتبطة
            $employees = Employee::with('department:id,name', 'user:id,email,name,phone,status','subject')
                ->where('role','employee')
                ->orWhere('role','teacher')
                ->where('department_id',$dept_id)
                ->get();

            // قم بتنسيق معلومات الموظفين وإرجاعها
        $formattedEmployees = $this->formatDate($employees);

        return response()->json($formattedEmployees, 200);
    }

    public function show($id)
    {
//            $employee =
    }

    private function formatDate($data)
    {
        $resultArray = [];

        foreach ($data as $item) {

            $sub_id = null;
            $dept_id = null;
            $sub_name = null;
            $dept_name = null;
            $resultArray[] = [
                'id' => $item['id'], //employee id
                'user_id' => $item['user']['id'],
                'avatarUrl' => '', // Add logic to get the avatar URL if available
                'name' => $item['user']['name'],
                'email' => $item['user']['email'],
                'status' => $item['user']['status'],
                'role' => $item['role'],
                'basic_salary' => $item['basic_salary'] ?? 0,
                'subject' =>[
                    'id' => $item['subject']['id'] ?? $sub_id ,
                    'name' => $item['subject']['name'] ?? $sub_name,
                ] ,
//                'department' => [
//                    'id' => $item['department']['id'] ?? $dept_id,
//                    'name' => $item['department']['name'] ?? $dept_name,
//                ],
            ];
        }
        return $resultArray;
    }



    public function update(Request $request, $id)
    {
        // ابحث عن الموظف المراد تحديثه
        $employee = Employee::find($id);

        // إذا لم يتم العثور على الموظف، قم بإرجاع رسالة خطأ
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // قم بتحديث معلومات الموظف
        $employee->department_id = $request->input('department_id') ?? $employee->department_id;
        $employee->basic_salary = $request->input('basic_salary') ?? $employee->basic_salary;
        $employee->subject_id = $request->input('subject_id') ?? $employee->subject_id;

        // قم بحفظ التغييرات
        $employee->save();

        // قم بتحديث معلومات المستخدم إذا كانت هناك تغييرات
        $user = $employee->user;
        if ($user) {
            $user->name = $request->input('name') ?? $user->name;
            $user->phone = $request->input('phone') ?? $user->phone;
            $user->address = $request->input('address') ?? $user->address;
            $user->email = $request->input('email') ?? $user->email;
            $user->status = $request->input('status') ?? $user->status;
            $user->save();
        }

        // قم بإرجاع رسالة نجاح
        return response()->json(['message' => 'Employee updated successfully'], 200);
    }

    public function destroy($id)
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
