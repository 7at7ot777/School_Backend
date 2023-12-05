<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public static $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'password' => 'required|string|max:255',
        'email' => 'required|email|unique:users|max:255',
        //========================================================
        'role_id' => 'required|exists:roles,id',
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


        'role_id.required' => 'The role ID field is required.',
        'role_id.exists' => 'The selected role does not exist.',

        'department_id.required' => 'The department ID field is required.',
        'department_id.exists' => 'The selected department does not exist.',

        'basic_salary.required' => 'The basic salary field is required.',
        'basic_salary.integer' => 'The basic salary must be an integer.',

        'subject_id.exists' => 'The selected subject does not exist.',

    ];

    public function index()
    {
        $employees = Employee::with('role:id,name', 'department:id,name', 'user:id,email,name,phone')->get();
        return response()->json($employees);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), self::$rules , self::$errorMessages);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $user = \App\Models\User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => bcrypt($request->password),
                'email' => $request->email,
                'user_type' => 'employee'
            ]);
            $user->save();

            $employee = Employee::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'basic_salary' => $request->basic_salary,
                'role_id' => $request->role_id,
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
        $employee = Employee::with('role:id,name', 'department:id,name', 'user:id,email,name,phone')->where('id', $id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        return $employee;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $roles = Role::all();
        $departments = Department::all();
        return response()->json(['employee' => $employee, 'roles' => $roles, 'departments' => $departments]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $employee->department_id = $request->department_id;
        $employee->basic_salary = $request->basic_salary;
        $employee->role_id = $request->role_id;
        $employee->subject_id = $request->subject_id;

        $employee->save();

        $user = $employee->user;

        // Update user information if needed
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->password = bcrypt($request->password);
        $user->email = $request->email;

        $user->save();

        return response()->json(['success' => 'Employee updated successfully'], 200);

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
