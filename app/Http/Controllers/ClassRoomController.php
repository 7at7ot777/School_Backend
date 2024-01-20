<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classRoom = ClassRoom::create($request->all());

        return response()->json($classRoom, 201);
    }

    public function update(Request $request, ClassRoom $classRoom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classRoom->update($request->all());

        return response()->json($classRoom, 200);
    }

    public function destroy(ClassRoom $classRoom)
    {
        $classRoom->delete();

        return response()->json(null, 204);
    }

    public function students(ClassRoom $classRoom)
    {
        $students = $classRoom->students;
        return response()->json($students, 200);
    }
}