<?php

namespace App\Http\Controllers;

use App\Imports\ImportClassroom;
use App\Models\ClassRoom;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

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
        $studentIds = $classRoom->students->pluck('user_id')->toArray();
        $students = User::with('student')->whereIn('id', $studentIds)->get();

        $formattedStudents = $students->map(function ($student) {
            return [
                'id' => $student->student->id,
                'user_id' => $student->id,
                'name' => $student->name,
                'phone' => $student->phone,
                'email' => $student->email,
            ];
        });
        return response()->json($formattedStudents, 200);
    }

    public function DownloadClassroomTemplate()
    {
        $filePath = public_path("storage/uploads/importClassroom.xlsx");
        $filename = 'importClassroom.xlsx';
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function importClassroom(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $importClassroom = new ImportClassroom();
            Excel::import($importClassroom, $file);
            return response()->json(['success', $importClassroom->counter . ' Classrooms imported successfully']);
        }
        return response()->json(['error' => 'No File Provided'], 401);
    }

    public function studentsInClass($class_id)
    {
        $students = User::where('user_type', 'student')
            ->whereHas('student.classroom', function ($query) use ($class_id) {
                $query->where('id', $class_id);
            })
            ->with(['student.father', 'student.mother', 'student.classroom', 'payments'])
            ->get();
        // التأكد مما إذا كان هناك طلاب متاحون
        if ($students->isEmpty()) {
            return response()->json(['message' => 'No students found'], 404);
        }

        // تنسيق بيانات الطلاب
        $formattedStudents = [];
        $studentController = new StudentController();
        foreach ($students as $student)
        {
            $formattedStudents[] = $studentController->formatStudent($student);
        }

        return $formattedStudents;
    }


}