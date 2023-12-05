<?php

namespace App\Http\Controllers;

use App\Models\Department;
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
        return Department::all();
    }

    public function show($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }

        return $department;
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

    public function update(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), self::$rules, self::$errorMessages);
        if ($validator->fails()) {
            return $validator->errors();
        } else {

            $department->update($request->all());
            return response()->json(['success' => 'Department updated successfully'], 200);
        }
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
