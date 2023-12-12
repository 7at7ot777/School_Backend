<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public static $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'password' => 'required|string|max:255',
        'email' => 'required|email|unique:users|max:255',
        //========================================================
//        'role_id' => 'required|exists:roles,id',
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

        //================================================================================


//        'role_id.required' => 'The role ID field is required.',
//        'role_id.exists' => 'The selected role does not exist.',

        'department_id.required' => 'The department ID field is required.',
        'department_id.exists' => 'The selected department does not exist.',

        'basic_salary.required' => 'The basic salary field is required.',
        'basic_salary.integer' => 'The basic salary must be an integer.',

        'subject_id.exists' => 'The selected subject does not exist.',

    ];

    public function index()
    {
        $admins = Employee::with('department:id,name', 'user:id,email,name,phone')->where('role', 'admin')->get();
//        return $admins;
        $formattedAdmins = $this->formatAdmins($admins);
        return response()->json($formattedAdmins);
    }

    private function formatAdmins($data)
    {

        $resultArray = [];

        foreach ($data as $item) {
            $resultArray[] = [
                'id' => $item['id'],
                'avatarUrl' => '', // Add logic to get the avatar URL if available
                'name' => $item['user']['name'],
                'email' => $item['user']['email'],
                'status' => true, // You can customize the status based on your criteria
                'department' => [
                    'id' => $item['department']['id'],
                    'name' => $item['department']['name'],
                ],

            ];
        }
        return $resultArray;

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $newRequest = $this->addDumpData($request,0);
//        return $request;
        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $user = \App\Models\User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => bcrypt($newRequest->password),
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $admin = Employee::with('department:id,name', 'user:id,email,name,phone')->where('id', $id)->first();
//        return $admin;

        if (!$admin) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
        $formattedAdmin = $this->formatAdmins([$admin]);

        return response()->json($formattedAdmin[0], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $roles = Employee::getRoles();
        $departments = Department::all();
        return response()->json(['employee' => $employee, 'roles' => $roles, 'departments' => $departments]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
         $this->addDumpData($request, 1);
        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
        $employee->department_id = $request->department_id;
        $employee->basic_salary = $request->basic_salary;
        $employee->role = 'admin';
        $employee->subject_id = $request->subject_id;

        $employee->save();

        $user = User::find($employee->user_id);
        // Update user information if needed
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->status = $request->status;
//        $user->password = bcrypt($request->password);
        if (strcmp($user->email ,$request->email) != 0) {
            $user->email = $request['swappedEmail'];
            }
        $user->save();

        return response()->json(['success' => 'Employee updated successfully'], 200);

    }
    private function addDumpData($request,$updateFlag = 0)
    {
        /* This function is used to add dump data to the request to escape from validation */
        if($request['password'] == null){

        $request['password'] = '123456789';
        }

        if($request['email'] != null && $updateFlag == 1){
            $request['swappedEmail'] = $request['email'];
            $request['email'] = random_int(0,99999999).'@gmail.com';

        }
        $request['basic_salary'] = 0;
        return $request;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $employee = \App\Models\Employee::find($id);
        $user = \App\Models\Employee::find($employee->user_id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $employee->delete();
        $user->delete();

        return response()->json(['success' => 'Employee deleted successfully'], 200);
    }

}
