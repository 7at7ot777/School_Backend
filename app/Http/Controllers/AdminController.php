<?php

namespace App\Http\Controllers;

use App\Imports\ImportAdmin;
use App\Models\ClassRoom;
use App\Models\Role;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{

    public function adminDashboard(){
         $emp = Auth::user()->employee;
        $admin = Employee::find($emp->id);
//        return $admin;
        //check the admin if found or it's not really admin

        switch ($admin->department_id)
        {
            case 4: //teaching staff
                $numOfTeachers = Employee::where('role','teacher')->count();
                $numOfSubjects = Subject::all()->count();
                return response()->json(['numOfTeachers' => $numOfTeachers, 'numOfSubjects' => $numOfSubjects]);
            case 5: //student affairs
                $numOfStudents = Student::all()->count();
                $numOfClassRooms = ClassRoom::all()->count();
                return response()->json(['numOfStudents' => $numOfStudents, 'numOfClassRooms' => $numOfClassRooms]);
            default:
                $numOfEmployyes = Employee::where('department_id',$admin->department_id)->where('role','employee')->count();
                return response()->json(['numOfEmployees' => $numOfEmployyes]);


        }

    }

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
        'basic_salary' => 'required|integer',
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

    public function index()
    {
        $admins = Employee::with('department:id,name', 'user:id,email,name,phone,status,avatar_url')
            ->where('role', 'admin')
            ->get();

        $formattedAdmins = $this->formatAdmins($admins);
        return response()->json($formattedAdmins, 200);
    }

    private function formatAdmins($data)
    {

        $resultArray = [];

        foreach ($data as $item) {

            $dept_id = null;
            $dept_name = null;
            $resultArray[] = [
                'id' => $item['user']['id'],
                'emp_id' => $item['id'],
                'avatarUrl' => $item['user']['avatar_url'] ?? '', // Add logic to get the avatar URL if available
                'name' => $item['user']['name'],
                'email' => $item['user']['email'],
                'status' => $item['user']['status'],
                'department' => [
                    'id' => $item['department']['id'] ?? $dept_id,
                    'name' => $item['department']['name'] ?? $dept_name,
                ],
            ];
        }
        return $resultArray;
    }


    public function store(Request $request)
    {
        $newRequest = $this->addDumpData($request, 0);
        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $user = \App\Models\User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => bcrypt('welcome'), //bcrypt($newRequest->password),
                'email' => $request['email'],
                'user_type' => 'employee'
            ]);
            $user->save();
            $employee = Employee::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'basic_salary' => $newRequest->basic_salary,
                'role' => 'admin',
                'subject_id' => $request->subject_id,
            ]);
            $employee->save();
        }
        return response()->json(['success' => 'Employee stored successfully'], 201);
    }

    public function show($id)
    {
        $admin = Employee::with('department:id,name', 'user:id,email,name,phone,status,avatar_url')
            ->where('id', $id)
            ->first();

        if (!$admin) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
        $formattedAdmin = $this->formatAdmins([$admin]);

        return response()->json($formattedAdmin[0], 200);
    }


    public function edit(Employee $employee)
    {
        $roles = Employee::getRoles();
        $departments = Department::all();
        return response()->json(['employee' => $employee, 'roles' => $roles, 'departments' => $departments], 200);
    }


    public function update(Request $request, $id)
    {
//        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
//        if ($validator->fails()) { return response()->json(['error' => $validator->errors()], 422); }
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
        $employee->department_id = $request->department_id;
        $employee->basic_salary = $request->basic_salary;
        $employee->role = 'admin';
//        $employee->subject_id = $request->subject_id;

        $employee->save();

        $user = User::find($employee->user_id);
        // Update user information if needed
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->status = $request->status;
        if (strcmp($user->email, $request->email) != 0) {
            $user->email = $request['swappedEmail'];
        }
        $user->save();
        return response()->json(['success' => 'Employee updated successfully'], 200);
    }


    private function addDumpData($request, $updateFlag = 0)
    {
        /* This function is used to add dump data to the request to escape from validation */
        if ($request['password'] == null) {

            $request['password'] = '123456789';
        }

        if ($request['email'] != null && $updateFlag == 1) {
            $request['swappedEmail'] = $request['email'];
            $request['email'] = random_int(0, 99999999) . '@gmail.com';
        }
        $request['basic_salary'] = 0;
        return $request;
    }




    public function destroy($id)
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

    public function DownloadAdminTemplate(){
        $filePath = public_path("storage/uploads/importAdmin.xlsx");
        $filename = 'importAdmin.xlsx';
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);

    }
    public function importAdmin(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $importAdmin = new ImportAdmin();
            Excel::import($importAdmin, $file);
            return response()->json(['success',$importAdmin->counter.' Admins imported successfully']);
        }
        return response()->json(['error', 'No File Provided'],401);

    }






    
}
