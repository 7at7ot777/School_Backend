<?php

namespace App\Http\Controllers;
use App\Models\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public static $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'password' => 'required|string|max:255',
        'email' => 'required|email|unique:users|max:255',
        'department_id' => 'required|exists:departments,id',
        'basic_salary' => 'required|integer',
        'subject_id' => 'required|exists:subjects,id',
    ];

    public static $errorMessages = [
        'name.required' => 'The name field is required',
        'name.string' => 'The name field must be a string',
        'name.max' => 'The name field must not exceed :max characters',

        'phone.string' => 'The phone field must be a string',
        'phone.max' => 'The phone field must not exceed :max characters',

        'address.string' => 'The address field must be a string',
        'address.max' => 'The address field must not exceed :max characters',

        'password.required' => 'The password field is required',
        'password.string' => 'The password field must be a string',
        'password.max' => 'The password field must not exceed :max characters',

        'email.required' => 'The email field is required',
        'email.email' => 'The email field must be a valid email address',
        'email.unique' => 'This email address is already in use',
        'email.max' => 'The email field must not exceed :max characters',

        'department_id.required' => 'The department number field is required',
        'department_id.exists' => 'Invalid department selected',

        'basic_salary.required' => 'The basic salary field is required',
        'basic_salary.integer' => 'The basic salary field must be an integer',

        'subject_id.required' => 'The subject ID field is required',
        'subject_id.exists' => 'Invalid subject selected',

    ];

    public function index()
    {
        $teachers = Employee::with('department:id,name', 'user:id,email,name,phone,status')->where('role', 'teacher')->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->get();
        $formattedTeachers = $this->formatTeachers($teachers);
        return response()->json($formattedTeachers, 200);
    }

    private function formatTeachers($data)
    {
        $resultArray = [];
        foreach ($data as $item) {
            $dept_id = null;
            $dept_name = null;
            $resultArray[] = [
                'id' => $item['id'],
                'avatarUrl' => '', 
                'name' => $item['user']['name'],
                'email' => $item['user']['email'],
                'status' => $item['user']['status'] == 0 ? false : true, 
                'department' => [
                    'id' => $item['department']['id'] ?? $dept_id,
                    'name' => $item['department']['name'] ?? $dept_name,
                ],
            ];
        }
        return $resultArray;
    }
    
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
{
    $teacher = Employee::with('subject')
        ->where('role', 'teacher')
        ->where('id', $id)
        ->first();

    if (!$teacher) {
        return response()->json(['error' => 'Teacher not found'], 404);
    }

    return response()->json(['data' => $teacher], 200);
}
}