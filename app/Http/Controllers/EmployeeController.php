<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\ImportEmployee;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

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
        $role = $request->subject_id == null || $request->subject_id == [] ? 'employee': 'teacher' ;

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
//            'subject_id' => $request->input('subject_id')
        ]);
        $subject_ids = $request->input('subject_id');
        if($role=='teacher')
        {
            $employee->subject()->sync($subject_ids);
        }

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

        if($dept_id == 4){
            // ابحث عن جميع الموظفين في القسم المحدد مع معلومات المستخدم المرتبطة
            $employees = Employee::with('subject:id,name', 'user:id,email,name,phone,status,avatar_url')
                ->where('role', 'teacher')
                ->whereHas('user', function ($query) {
                    $query->where('status', 1);
                })->get();
        }else{
            // ابحث عن جميع الموظفين في القسم المحدد مع معلومات المستخدم المرتبطة
            $employees = Employee::with('department:id,name', 'user:id,email,name,phone,status')
                ->where('role','employee')
                ->where('department_id',$dept_id)
                ->get();
        }


            // قم بتنسيق معلومات الموظفين وإرجاعها
        $formattedEmployees = $this->formatDate($employees,$dept_id);

        return response()->json($formattedEmployees, 200);
    }

    public function show($id)
    {
        $employee = Employee::with('department:id,name', 'user:id,email,name,phone,status','subject')
            ->Where('id',$id)
            ->first();

        if(!$employee)
        {
            return response()->json(['error'=>'Employee not found '], 404);

        }

        $formattedEmployee = $this->formatDate($employee);

        return response()->json($formattedEmployee, 200);


    }

    private function formatDate($data,$dept_id)
    {
        $resultArray = [];

        if($dept_id == 4) {
            $resultArray = [];
            foreach ($data as $item) {
                $subjects = [];
                $objSub = $item->subject;

                foreach ($objSub as $subject) {

                    $subjects[] = [
                        'id' => $subject->id,
                        'name' => $subject->name,
                    ];

//
                }
                $resultArray[] = [
                    'teacher_id' => $item['id'],
                    'id' => $item['user']['id'],
                    'avatarUrl' => $item['user']['avatar_url'] ?? '',
                    'name' => $item['user']['name'],
                    'email' => $item['user']['email'],
                    'status' => $item['user']['status'] == 0 ? false : true,
                    'subject' => $subjects,
                ];

            }
        }else{
        foreach ($data as $item) {

            $sub_id = null;
            $dept_id = null;
            $sub_name = null;
            $dept_name = null;
            $resultArray[] = [
                'emp_id' => $item['id'], //employee id
                'id' => $item['user']['id'],
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
        }
        return $resultArray;
    }

    public function update(Request $request, $id)
    {
        // Find the employee to be updated
        $user = User::find($id);
        $employee = Employee::findOrFail($user->employee->id);

        // Validate the request data
//        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
//        if ($validator->fails()) {
//            return $validator->errors();
//        }

        // Update the user associated with the employee
        $employee->user->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'email' => $request->input('email'),
        ]);

        // Update the employee details
        $employee->update([
            'department_id' => $request->input('department_id') ?? $employee->department_id,
            'basic_salary' => $request->input('basic_salary') ?? $employee->basic_salary,
        ]);

        // Update the employee role based on subject_id
        $role = $request->subject_id == null || $request->subject_id == [] ? 'employee' : 'teacher';
        $employee->update(['role' => $role]);

        // If the role is teacher, sync the subjects
        if ($role == 'teacher') {
            $subject_ids = $request->input('subject_id');
            $employee->subject()->sync($subject_ids);
        }

        // Return a success message
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

    public function DownloadEmployeeTemplate()
    {
        $filePath = public_path("storage/uploads/importEmployee.xlsx");
            $filename = 'importEmployee.xlsx';
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function importEmployee(Request $request,$dept_id){
        $exists = Department::where('id', $dept_id)->exists();

        if ($exists) {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $importEmployee = new ImportEmployee($dept_id);
                Excel::import($importEmployee, $file);
                return response()->json(['success', $importEmployee->counter . ' Employees imported successfully']);
            }
            return response()->json(['error' => 'No File Provided'], 401);
        }
        return response()->json(['error' => 'Department Doesn\'t Exitst'], 404);

    }

}
