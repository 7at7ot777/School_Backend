<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LectureController extends Controller
{
    public function index()
    {
        $teacher = Employee::where('user_id', 2)->first();
        $lectures = Lecture::with(['employee.user', 'subject'])->where('employee_id', $teacher->id)->get();
        $formatedLecture = $this->formatData($lectures);
        return response()->json($formatedLecture);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'subject_id' => 'required',
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $lecture = new Lecture();
        $lecture->employee_id = $request->employee_id;
        $lecture->user_id = Auth::id();
        $lecture->title = $request->title;
        $lecture->url = $request->url;
        $lecture->description = $request->description;
        $lecture->subject_id = $request->subject_id;
        $lecture->save();


        return response()->json(['success' => 'Lecture is added successfully'], 201);
    }

    // Display the specified resource.
    public function show($id)
    {
        $lecture = Lecture::with(['employee.user', 'subject'])->findOrFail($id);
        $formatedLecture = $this->formatData([$lecture]);
        return response()->json($formatedLecture[0]);
    }

    public function formatData($data)
    {
        $formattedLecture = [];
        foreach($data as $lecture)
        {
            $formattedLecture[] = [
                'id' => $lecture->id,
                'subject_id' => $lecture->subject_id,
                'employee_id' => $lecture->employee_id,
                'user_id' => $lecture->user_id,
                'title' => $lecture->title,
                'url' => $lecture->url,
                'description' => $lecture->description,
                'employee' => [
                    'id' => $lecture->employee->id,
                    'role' => $lecture->employee->role,
                ],
                'subject' => [
                    'id' => $lecture->subject->id,
                    'name' => $lecture->subject->name,
                ],
            ];
        }
        return $formattedLecture;
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'subject_id' => 'required',
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        // Find the lecture by its ID
        $lecture = Lecture::find($id);

        // Check if the lecture exists
        if (!$lecture) {
            return response()->json(['error' => 'Lecture not found'], 404);
        }

        // Update the lecture attributes
        $lecture->employee_id = $request->employee_id;
        $lecture->user_id = Auth::id();
        $lecture->title = $request->title;
        $lecture->url = $request->url;
        $lecture->description = $request->description;
        $lecture->subject_id = $request->subject_id;
        $lecture->save();

        return response()->json(['success' => 'Lecture updated successfully'], 200);
    }


    // Remove the specified resource from storage.
    public function destroy($id)
    {
        $lecture = Lecture::find($id);

        // Check if the lecture exists
        if (!$lecture) {
            return response()->json(['error' => 'Lecture not found'], 404);
        }

        // Delete the lecture
        $lecture->delete();

        // Return a success response with a status code of 200 OK
        return response()->json(['success' => 'Lecture deleted successfully'], 200);
    }
}
