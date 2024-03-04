<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassRoomController extends Controller
{
    public function index()
    {
        $classRooms = ClassRoom::all();
        return response()->json($classRooms, 200);
    }

    public function show(ClassRoom $classRoom)
    {
        return response()->json($classRoom, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_number' => 'required|integer',
            'grade' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $classRoom = ClassRoom::create($request->all());
        return response()->json(['success' => 'ClassRoom stored successfully'], 201);
    }

    public function update(Request $request, ClassRoom $classRoom)
    {
        $validator = Validator::make($request->all(), [
            'class_number' => 'required|integer',
            'grade' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $classRoom->update($request->all());
        return response()->json(['success' => 'ClassRoom updated successfully'], 200);
    }

    public function destroy(ClassRoom $classRoom)
    {
        $classRoom->delete();
        return response()->json(['success' => 'ClassRoom deleted successfully'], 200);
    }

    public function students(ClassRoom $classRoom)
{
    $students = $classRoom->students;
    return response()->json($students, 200);
}
}