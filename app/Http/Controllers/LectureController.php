<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LectureController extends Controller
{
    public function index()
    {
        $teacher = Employee::where('user_id',Auth::id())->first();
        $lectures = Lecture::with(['employee', 'subjects'])->where('employee_id',$teacher->id)->get();
        return response()->json($lectures);
    }

        // Store a newly created resource in storage.
        public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'required|string',
        ]);

        $lecture = new Lecture();
        $lecture->employee_id = $request->employee_id;
        $lecture->title = $request->title;
        $lecture->url = $request->url;
        $lecture->description = $request->description;
        $lecture->save();

        // Associate subjects with the lecture
        $lecture->subjects()->sync($request->subject_ids);

        return response()->json($lecture, 201);
    }

        // Display the specified resource.
        public function show($id)
    {
        $lecture = Lecture::with(['employee', 'subjects'])->findOrFail($id);
        return response()->json($lecture);
    }

        // Update the specified resource in storage.
        public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'exists:employees,id',
            'subject_ids' => 'array',
            'subject_ids.*' => 'exists:subjects,id',
            'title' => 'string|max:255',
            'url' => 'url',
            'description' => 'string',
        ]);

        $lecture = Lecture::findOrFail($id);
        $lecture->update($request->only(['employee_id', 'title', 'url', 'description']));

        // If subject_ids are provided, update the associated subjects
        if ($request->has('subject_ids')) {
            $lecture->subjects()->sync($request->subject_ids);
        }

        return response()->json($lecture);
    }

        // Remove the specified resource from storage.
        public function destroy($id)
    {
        $lecture = Lecture::findOrFail($id);
        $lecture->subjects()->detach(); // Detach all subjects related to this lecture
        $lecture->delete();

        return response()->json(null, 204);
    }
}
