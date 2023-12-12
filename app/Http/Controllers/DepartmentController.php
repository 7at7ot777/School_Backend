<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    private static $rules = [
        'name' => 'required|string|unique:departments|max:255',
    ];
    private static $errorMessages = [
        'name.required' => 'The department name field is required.',
        'name.string' => 'The name field must be a string.',
        'name.unique' => 'The specified department name is already taken.',
        'name.max' => 'The department name field must not exceed 255 characters.',
        ];
        public function index()
        {
            $departments = Department::with(['employees' => function ($query) {
                $query->with('user')->where('role','admin')->oldest();
            }])->get();

            $formatedData = $this->formatData($departments);
        
            return response()->json($formatedData);
        }
        public function formatData($data)
        {
            $resultArray = [];

            foreach ($data as $department) {
                $mainAdmin = $department->employees->first();

                $resultArray[] = [
                    'id' => $department->id,
                    'name' => $department->name,
                    'numOfAdmins' => $department->employees->where('role', 'admin')->count(),
                    'numOfEmps' => $department->employees->count(),
                    'mainAdmin' => [
                        'name' => $mainAdmin ? $mainAdmin->user->name : '',
                        'avatarUrl' => $mainAdmin ? $mainAdmin->user->avatar_url : '',
                    ],
                ];
            }
            return $resultArray;
        }

    public function show($id)
    {
        $department = Department::with(['employees' => function ($query) {
            $query->with('user')->where('role','admin');
        }])->findOrFail($id);
    
       if (!$department)
       {
           return response()->json(['error' => 'Department not found'], 404);

       }
       $formatedData = $this->formatData([$department]);
    
        return response()->json($formatedData[0],200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), self::$rules , self::$errorMessages);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            Department::create($request->all());
        return response()->json(['success' => 'Department stored successfully'], 201);
        }
    }

    public function update(Request $request, Department $department, $id)
    {
    $validatedData = $request->validated();
    $department = Department::findOrFail($id);
    $department->update($validatedData);
    $admins = User::whereHas('role', function ($query) {
        $query->where('name', 'admin');
    })
    ->where('department_id', $department->id)
    ->get();

    $data = [
        'department_name' => $department->name,
        'admin_name' => $admins->implode('name', ', '), 
        'employee_count' => count($department->employees),
        'employees' => $department->employees,
    ];

    return response()->json($data);
    }

    
    public function destroy($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }

        $department->delete();

        return response()->json(['success' => 'Department deleted successfully'], 200);
    }
}
