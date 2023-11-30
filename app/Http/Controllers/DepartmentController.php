<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
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
         Department::create($request->all());
        return response()->json(['success' => 'Department stored successfully'], 201);
    }

    public function update(Request $request, Department $department)
    {
        $department->update($request->all());
        return response()->json(['success' => 'Department updated successfully'], 200);
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
