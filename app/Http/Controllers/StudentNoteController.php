<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Student;
use App\Models\StudentNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentNoteController extends Controller
{

    public function store(Request $request)
    {

        $teacher = Employee::where('user_id', Auth::id())->first();
        $student = Student::with('father','mother')->where('user_id', $request->student_id)->first();
        $father_id = $student->father->id;
        $mother_id = $student->mother->id;

        $note = StudentNote::create([
            'subject_id' => $request->subject_id,
            'teacher_id' => $teacher->id,
            'father_id' => $father_id,
            'mother_id' => $mother_id,
            'student_id' => $request->student_id,
            'note' => $request->note,
        ]);
        $note->save();
        return response()->json(['success' => 'Note is added successfully']);
    }

    //Only Update the Note
    public function update(Request $request, $id)
    {
        // Find the note by ID
        $note = StudentNote::find($id);

        // If the note doesn't exist, return an error response
        if (!$note) {
            return response()->json(['error' => 'Note not found'], 404);
        }

        // Optionally, verify the teacher is the one who originally created the note
        $teacher = Employee::where('user_id', Auth::id())->first();
        if ($note->teacher_id !== $teacher->id) {
            return response()->json(['error' => 'Unauthorized to update this note'], 403);
        }

        // Update the note with new information from the request
        $note->update([
//            'subject_id' => $request->input('subject_id', $note->subject_id), // Keeps existing value if not provided
            'note' => $request->input('note', $note->note), // Keeps existing value if not provided
        ]);

        // Return a success response
        return response()->json(['success' => 'Note updated successfully']);
    }

    public function destroy($id)
    {
        $note = StudentNote::find($id);
        if (!$note) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $note->delete();
        return response()->json(['message' => 'Deleted successfully'], 200);
    }

    public function show($id)
    {
        $note = StudentNote::find($id);
        if (!$note) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($note->note);
    }

    public function showAllNotesForSpecificStudent($student_id)
    {
//        return $student_id;
        $notes = StudentNote::select('id','note')->where('student_id',$student_id)->get();
        return response()->json($notes);
    }

    public function showAllNotesFor1StudentAnd1Subject($student_id,$subject_id)
    {
        $notes = StudentNote::select('id','note')->where('student_id',$student_id)->where('subject_id',$subject_id)->get();
        return response()->json($notes);
    }



}
