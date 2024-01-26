<?php

namespace App\Http\Controllers;

use App\Imports\ImportDepartment;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

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
                $query->with('user')->where('role','admin')->orWhere('role','employee')->oldest();
            }])->get();

//            $numOfActiveAdmins = Employee::where('role','admin')->where('is_active',1)->count();
//            $numOfActiveEmployees = Employee::where('role','employee')->where('is_active',1)->count();

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
                    'numOfAdmins' => $department->employees->where('role', 'admin')->where('is_active',1)->count(),
                    'numOfEmps' => $department->employees->where('role','employee')->where('is_active',1)->count(),
                    'mainAdmin' => [
                        'id' => $mainAdmin ? $mainAdmin->user->id : '',
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

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), self::$rules , self::$errorMessages);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $department = Department::findOrFail($id);
            if (!$department) {
                return response()->json(['error' => 'Department not found'], 404);
            }
            $department->name = $request->name;
            $department->save();

            return response()->json(['success' => 'Department updated successfully'], 200);
        }}


    
    public function destroy($id)
    {
      $department =  Department::with(['employees' => function ($query) {
            $query->where('role', 'admin');
        }])->where('id',$id)->first();
        if (!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }
//        $department->employees->department_id = null ;
//        $department->save();
        $department->delete();

        return response()->json(['success' => 'Department deleted successfully'], 200);
    }

    public function importDepartment(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            Excel::import(new ImportDepartment(), $file);
            return response()->json(['success', 'File imported successfully']);
        }
        return response()->json(['error', 'No File Provided'],401);

    }

    public function DownloadDepartmentTemplate()
    {
        $filePath = public_path("storage/uploads/importDepartment.xlsx");
//        return $filePath;

        return response()->download($filePath, 'importDepartment.xlsx');
    }
}
